<?php 

namespace App\enums; 

enum SourceLocationPaths: string 
{
    /* For database purposes */
    case PATH_TO_MYSQLDUMP = "C:/xampp/mysql/bin/";
    case PATH_TO_BKUP_DIR = "/xampp/htdocs/icuc_ars/database/backup/";
    case BASE_PROJECT_DIR_NAME = "icuc_ars";
}