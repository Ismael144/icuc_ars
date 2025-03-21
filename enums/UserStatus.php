<?php 

namespace App\enums; 

enum UserStatus: int 
{
    case INACTIVE = 0;

    case ACTIVE = 1; 
}