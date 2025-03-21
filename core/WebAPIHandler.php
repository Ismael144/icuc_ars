<?php

namespace App\core;

class WebAPIHandler
{
    public int $statusCode;

    public readonly object $request;

    public function __construct()
    {
        $this->request = (object)$this->createArray(
            status: http_response_code(),
            method: $_SERVER['REQUEST_METHOD'],
            headers: getallheaders(),
        );
    }

    public function getPostData(): mixed
    {
        // Receive JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Decode JSON data into PHP associative array
        $data = json_decode($json_data, true);

        return $data;
    }

    public function requestMethod(string $requestMethod): bool
    {
        if (strtolower($this->request->method) == strtolower($requestMethod)) {
            return true;
        }
        return false;
    }

    public function purifyData(string $value): mixed
    {
        return trim(
            mb_convert_encoding(
                htmlspecialchars(
                    html_entity_decode($value)
                ),
                "utf-8"
            )
        );
    }

    public function changeStatusCode(int $statusCode)
    {
        http_response_code($statusCode);
        return $statusCode;
    }

    /**
     * Creates arrays inform of objects
     *
     * @param ...$response
     * @return array
     */
    public static function createArray(...$response)
    {
        $resArray = [];
        foreach ($response as $key => $value) {
            $resArray[$key] = $value;
        }

        return $resArray;
    }

    public static function jsonEncode(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    public static function jsonDecode(string $value): mixed
    {
        return json_decode($value);
    }

    public function getRequestData(string $key, string $method = 'get'): mixed
    {
        return match (strtolower($method)) {
            'all'     => $_REQUEST,
            'get'     => isset($_GET[$key]) ? $_GET[$key] : null,
            'post'    => isset($this->getPostData()[$key]) ? $this->getPostData()[$key] : null,
            'files'   => isset($_FILES[$key]) ? $_FILES[$key] : null,
            'cookie'  => isset($_COOKIE[$key]) ? $_COOKIE[$key] : null,
            'session' => isset($_SESSION[$key]) ? $_SESSION[$key] : null,
            default   => isset($_GET[$key]) ? $_GET[$key] : null,
        };
    }

    public function outputResponse(mixed $data): void
    {
        echo self::jsonEncode($data);
    }

    /**
     * Get a header by its key
     *
     * @param string $key
     * @return ?string
     */
    public function getHeader(string $key): string | null
    {
        return isset($this->request->headers[$key]) ? $this->request->headers[$key] : null;
    }

    /**
     * 
     * Sends request headers
     * 
     * @param array $headers
     * @return void
     */
    public function sendHeaders(array $headers): void
    {
        if (headers_sent() || empty($headers)) return;
        foreach ($headers as $header => $value) {
            header(trim($header) . ': ' . trim($value));
        }
    }

    /** 
     * Initialize and setup API
     * 
     * @param callable $actions 
     * @return void
     */
    public function init(callable $actions): void
    {
        // Setting some necessary headers
        $this->sendHeaders(["Content-Type" => "application/json"]);
        $this->sendHeaders(["Access-Control-Allow-Origin" => "*"]);

        // Setting up the timezone incase its not right
        date_default_timezone_set('Africa/Kampala');

        $actions($this);
    }
}
