<?php 

namespace App\helpers; 

use App\core\Helper;

class SessionHelper extends Helper 
{   
    public function __construct()
    {
        if (session_id() == null) session_start();
    } 

    /**
     * Checks whether a session exists
     *
     * @param string $sessionKey
     * @return boolean
     */
    public function isValid(string $sessionKey): bool
    {   
        return isset($_SESSION[$sessionKey]);
    }

    /**
     * Creates a session and assigns a key to it
     * 
     * ```php 
     *  # Example
     *  <?php 
     *    $sessionHelper->set(myKey: "<Some Value>");
     *  ?>
     * ```
     *
     * @param string $sessionKey
     * @param mixed $value
     * @return mixed
     */
    public function set(...$keyValueArgs): mixed
    {
        foreach($keyValueArgs as $sessionKey => $value) {
            $_SESSION[$sessionKey] = $value;
            return $value; 
        }

        return null; 
    }

    /**
     * Deletes a session
     *
     * @param string $sessionKey
     * @return void
     */
    public function unset(string ...$sessionKeys)
    {
        foreach($sessionKeys as $sessionKey) unlink($_SESSION[$sessionKey]);
    }

    /**
     * Gets session by session array key, if does not exist, then returns null 
     *
     * @param string $sessionKey
     * @return mixed
     */
    public function get(string $sessionKey): mixed
    {
        return $this->isValid($sessionKey) ? $_SESSION[$sessionKey] : null; 
    }

    /**
     * Destroys all sessions
     *
     * @return void
     */
    public function destroyAll(): void
    {
        session_destroy();
    }

    public function all(): array
    {
        return $_SESSION;
    }
}