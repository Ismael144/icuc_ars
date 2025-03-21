<?php 

namespace App\exceptions\user;

use Exception; 

class InvalidUserRoleException extends Exception 
{
    public function __construct(string $role)
    {
        $this->message = "Role '$role' is invalid"; 
    }
}