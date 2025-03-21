<?php 

namespace App\enums; 

enum DatabaseCredentials: string 
{
    case DBHOST = "localhost"; 
    case DBNAME = "icuc_arm_system";
    case USERNAME = "root";
    case PASSWORD = "";
}