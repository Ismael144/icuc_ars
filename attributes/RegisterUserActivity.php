<?php 

namespace App\attributes;

#[\Attribute]
class RegisterUserActivity
{
    public function __construct(
        public string $message = ""
    ) {
        echo $message;
        // exit; 
    }
}