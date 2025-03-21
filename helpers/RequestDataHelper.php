<?php 

namespace App\helpers; 

use App\core\Helper;

class RequestDataHelper extends Helper 
{
    public static function getPreviousLink(): string 
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public static function get(string $key): mixed 
    {
        if (isset($_GET[$key])) return $_GET[$key]; 
        return null; 
    }

    public static function post(string $key): mixed 
    {
        if (isset($_POST[$key])) return $_POST[$key]; 
        return null; 
    }

    public static function method($method)
    {
        return $_SERVER['REQUEST_METHOD'] == $method; 
    }
}