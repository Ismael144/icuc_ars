<?php

require dirname(__DIR__) . "/../vendor/autoload.php";

use App\Core\WebAPIHandler;
use App\GFS\GFServiceManager;
use App\controllers\AttendanceDataController;

$webAPIHandler = new WebAPIHandler();

// API that does geofencing analysis (Check whether a given pair of coordinates are in the given fence or premises)
$webAPIHandler->init(function (WebAPIHandler $appHandler) {
    $geofenceManager = new GFServiceManager;
    $attendanceDataController = new AttendanceDataController;

    if ($appHandler->requestMethod('POST')) {
        $attendanceSettings = $attendanceDataController->getAttendanceSettings();

        // This should be an associated array having keys lat and lng, [lat => ..., lng => ...]
        $postData = $appHandler->getPostData();

        // Checking whether the coordinates are in within the area of fence
        $results = $geofenceManager->coordinateInGeofences($geofenceManager->createCoordinate($postData['coordinates']['lat'], $postData['coordinates']['lng']));

        $results = in_array(true, $results);

        $mode = "";
        $isCheckIn = false;

        $staffDataId = $postData['staffId'];
        // Attendance record for specific user 
        $userMarkedAttendance = $attendanceDataController->checkAttendanceExistence(["staff_data_id" => $staffDataId, "date_attended" => date('Y-m-d')], true);

        if ($results && $attendanceDataController->isCheckinOrCheckoutTime()['check_in']) {
            $mode = "Checked In";

            // Doing the geofence check
            if ($results) {
                $isAttendanceMarked = $attendanceDataController->checkAttendanceExistence(["staff_data_id" => $staffDataId, "date_attended" => date("Y-m-d")]);

                if (!$isAttendanceMarked) {
                    $attendanceDataController->registerAttendance(["staff_data_id" => $staffDataId, "arrival_time" => date('H:i:s'), "departure_time" => NULL]);
                    $isCheckIn = true;
                }
            }
        }

        if (!$results && $attendanceDataController->isCheckinOrCheckoutTime()['check_in']) {
            $appHandler->outputResponse($appHandler::jsonEncode(["results" => $results, "mode" => $mode]));
            return;
        }

        if ($attendanceDataController->isCheckinOrCheckoutTime()['check_out']) {
            $mode = "Checked Out";
            if (count($userMarkedAttendance)) {
                $attendanceDataController->attendanceDataModel->preparedUpdate(["departure_time" => date('H:i')], ["staff_data_id" => $staffDataId]);
            }
            $isCheckIn = false;
        }

        // Return the results
        $userMarkedAttendance = $attendanceDataController->checkAttendanceExistence(["staff_data_id" => $staffDataId, "date_attended" => date('Y-m-d')], true);

        $fixedTime = $attendanceDataController->time_format($userMarkedAttendance["arrival_time"], "H:i");

        $elapsed = $attendanceDataController->attendanceDataModel->getTimeElapsed($fixedTime, $attendanceSettings['arrival_time']);

        $userMarkedAttendance['arrival_time'] = $attendanceDataController->_time_format_to_am_pm($userMarkedAttendance['arrival_time'], "--:--");
        $userMarkedAttendance['departure_time'] = $attendanceDataController->_time_format_to_am_pm($userMarkedAttendance['departure_time'], "--:--");
        $userMarkedAttendance['time_late'] = $attendanceDataController->interpreteTimeLate($elapsed);

        $appHandler->outputResponse($appHandler::jsonEncode(["results" => $results, "attendanceResults" => $userMarkedAttendance, "mode" => $mode, "is_check_in" => $isCheckIn]));
    } else {
        $appHandler->outputResponse($appHandler::jsonEncode(["code" => $appHandler->changeStatusCode(405), "message" => "Method '" . $appHandler->request->method . "' Not Allowed"]));
    }

});
