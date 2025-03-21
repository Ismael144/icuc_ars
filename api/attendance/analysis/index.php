<?php

use App\controllers\AttendanceDataAnalysisController;
use App\core\WebAPIHandler;

require dirname(__DIR__) . "/../../vendor/autoload.php";

$webAPIHandler = new WebAPIHandler;

$actions = function (WebAPIHandler $webAPIHandler) {
    $attendanceAnalysisController = new AttendanceDataAnalysisController(departmentId: 7);
    $webAPIHandler->outputResponse($attendanceAnalysisController->getAttendanceDataForUsersInGivenDept());
    echo "\n";
    $webAPIHandler->outputResponse($attendanceAnalysisController->getAttendanceExcusesInformationForGivenDept());
    echo "\n";
    $attendanceAnalysisController->getTotalAttendanceTimeForGivenDept();
    echo "\n";
    $attendanceAnalysisController->getNumberOfHolidaysAttendedOrUnattended();
    echo "\n"; 
    var_dump($attendanceAnalysisController->getNumberOfHolidaysAttendedOrUnattendedFilteredByDate());
    var_dump($attendanceAnalysisController->getAttendanceExcusesInformationForGivenDept());
    echo "Separator: ----------------<br>";
    var_dump($attendanceAnalysisController->getAttendanceRateForGivenDept("2024"));
};

$webAPIHandler->init($actions);

