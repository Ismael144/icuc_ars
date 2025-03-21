<?php 

require_once dirname(__DIR__)."/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\StaffDataController;

$appAPIHandler = new WebAPIHandler;
$staffDataController = new StaffDataController; 

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use($staffDataController) {
    // Organize data     
    $content = file_get_contents(dirname(__DIR__)."/../sockets/system_stats.json");
    echo $content; 
});