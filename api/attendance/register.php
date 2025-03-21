<?php

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\AttendanceDataController;

$appAPIHandler = new WebAPIHandler;
$attendanceController = new AttendanceDataController;

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use ($attendanceController) {
    if ($appAPIHandler->requestMethod('POST')) {
        $attendanceData = $appAPIHandler->getPostData();
    }

    if ($appAPIHandler->requestMethod('PATCH')) {
        $updateAttendanceData = $appAPIHandler->getPostData();
        $date = !isset($updateAttendanceData['date_attended']) ? date("Y-m-d") : $updateAttendanceData['date'];
        $staffDataId = $updateAttendanceData['staff_data_id'];
    }

    if ($appAPIHandler->getRequestData("mode") == "bulk" && $appAPIHandler->requestMethod('PUT')) {
        $attendanceBulkData = $appAPIHandler->getPostData();
        $response = $attendanceController->mergeAttendanceDataFromFRSystem($attendanceBulkData);
        
        $appAPIHandler->outputResponse(["is_successful" => $response]); 
    }
});
