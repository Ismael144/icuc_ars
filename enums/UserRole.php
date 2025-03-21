<?php 

namespace App\enums; 

enum UserRole: int 
{
    case SYSTEM_ADMINISTRATOR = 1;

    case STAFF_MEMBER = 2; 
}
