<?php
require_once dirname(__DIR__) . "/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\models\StaffDataImage;
use App\controllers\StaffDataController;
use App\controllers\AttendanceDataController;
use App\controllers\JSONSettingsController;

$appAPIHandler = new WebAPIHandler;
$staffDataController = new StaffDataController;
$attendanceDataController = new AttendanceDataController;


$actions = function (WebAPIHandler $appAPIHandler) use ($attendanceDataController, $staffDataController) {
    $JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__)) . "/settings/attendance_settings.json");
    $attendanceTime = $JSONSettingsController->getSetting("attendanceTime");

    // Get today's date
    $currentDate = $attendanceDataController->attendanceDataModel->currentDate;

    // Fetching all records with the current date_attended
    $currentDateAttendanceData = $attendanceDataController->attendanceDataModel->getRecordsBy('date_attended', $currentDate, multiple: true);
    
    // Do some formatting here 
    $currentDateAttendanceData = array_map(function ($item) use ($attendanceDataController, $staffDataController, $attendanceTime) {
        // Getting data for single staff member
        $staffData = $staffDataController->staffDataModel->getRecordsBy("id", $item["staff_data_id"], what: ["concat(first_name, ' ', last_name) as fullName"]);

        // Getting a single image for every single staff member
        $staffMemberImages = array_map(function ($image) {
            return "http://localhost/icuc_ars/images/staff_images/" . $image['name'];
        }, (new StaffDataImage)->getImagesBy($item["staff_data_id"]));
        $item["image"] = count($staffMemberImages) ? $staffMemberImages[0] : "http://localhost/icuc_ars/app/assets/images/male-img.jpg";

        // Knowing How late the staff member was
        $fixedTime = $attendanceDataController->time_format($item["arrival_time"], "H:i");
        $elapsed = $attendanceDataController->attendanceDataModel->getTimeElapsed($fixedTime, $attendanceTime["arrival_time"]);

        // Checking whether they arrived in time or not 
        if ($elapsed['hours'] < 0 || $elapsed['minutes'] < 1) {
            $item["time_late"] = "Arrived In Time";
        } else {
            if ($elapsed['hours'] > 0) {
                if ($elapsed['minutes'] > 0) {
                    $item["time_late"] = "{$elapsed['hours']} hours and {$elapsed['minutes']} minutes late";
                } else {
                    $item["time_late"] = "{$elapsed['hours']} hours late";
                }
            } else {
                $item["time_late"] = "{$elapsed['minutes']} minutes late";
            }
        }

        $item["arrival_time"] = $attendanceDataController->_time_format_to_am_pm($item["arrival_time"]);
        $item["departure_time"] = $attendanceDataController->_time_format_to_am_pm($item["departure_time"], "__:__");

        return array_merge($item, $staffData);
    }, $currentDateAttendanceData);

    $appAPIHandler->outputResponse($currentDateAttendanceData);
};

$appAPIHandler->init($actions);
