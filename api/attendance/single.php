<?php

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\AttendanceDataController;

$appAPIHandler = new WebAPIHandler;
$attendanceDataController = new AttendanceDataController;

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use ($attendanceDataController) {
    // Get the staff_data_id and the date 
    if ($appAPIHandler->requestMethod('GET')) {
        $staffDataId = $appAPIHandler->getRequestData('staff_id');
        $attendanceDate = $appAPIHandler->getRequestData('date_attended');
        $getData = is_null($appAPIHandler->getRequestData('data')) ? False : True;

        $results = $attendanceDataController->checkAttendanceExistence(['staff_data_id' => $staffDataId, 'date_attended' => $attendanceDate], $getData); 

        $appAPIHandler->outputResponse(["results" => $results]); 
    }
});
