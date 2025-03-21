<?php

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\AttendanceDataController;
use App\controllers\JSONSettingsController;

$appAPIHandler = new WebAPIHandler;
$attendanceDataController = new AttendanceDataController;
$jsonSettingsController = new JSONSettingsController("../../settings/attendance_settings.json"); 

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use ($attendanceDataController, $jsonSettingsController) {
    $appAPIHandler->outputResponse($jsonSettingsController->readSettings()['attendanceTime']); 
});
