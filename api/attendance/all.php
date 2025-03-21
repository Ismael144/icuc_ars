<?php 

require_once dirname(__DIR__)."/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\AttendanceDataController; 

$appAPIHandler = new WebAPIHandler;
$attendanceController = new AttendanceDataController; 

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use($attendanceController) {
    // Organize data 
    $attendanceData = $attendanceController->getDataForAPI(); 
    echo $appAPIHandler->jsonEncode($attendanceData);
});