<?php
$pageTitle = "Staff All Data";
include dirname(__DIR__) . "/includes/header.php";
?>

<?php include dirname(__DIR__) . "/includes/layouts/topbar.php" ?>
<?php include dirname(__DIR__) . "/includes/layouts/sidebar.php" ?>

<?php

use App\controllers\StaffDataController;
use App\controllers\JSONSettingsController;
use App\controllers\AttendanceDataController;

$staffDataController = new StaffDataController;

$attendanceDataController = new AttendanceDataController;

$JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__)) . "/settings/attendance_settings.json");
$attendanceTime = $JSONSettingsController->getSetting("attendanceTime");

$attendanceTableData = $attendanceDataController->getTableData();

$attendanceTableData = array_map(function ($item) use($attendanceDataController, $staffDataController, $attendanceTime) {
    // Getting data for single staff member
    $staffData = $staffDataController->staffDataModel->getRecordsBy("id", $item["staff_id"], what: ["concat(first_name, ' ', last_name) as fullName"]);

    // Knowing How late the staff member was
    $fixedTime = $attendanceDataController->time_format($item["arrival_time"], "H:i");
    $elapsed = $attendanceDataController->attendanceDataModel->getTimeElapsed($fixedTime, $attendanceTime["arrival_time"]);


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
    $item["departure_time"] = $attendanceDataController->_time_format_to_am_pm($item["departure_time"]);

    return array_merge($item, $staffData);
}, $attendanceTableData);

// $webController->generateDataTable($attendanceTableData, "Attendance Data");


?>

<div class="main-content">
    <div class="page-content">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Archive</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item active">All Data</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"></h4>
                </div>
                <div class="card-body pt-1">
                    <div class="d-flex align-items-center flex-grow-1 justify-content-between mb-3">
                        <div class="operations">
                            <div class="dropdown open">
                                <button class="btn btn-success dropdown-toggle" type="button" id="more-button-dropdown"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    More
                                </button>
                                <div class="dropdown-menu" aria-labelledby="more-button-dropdown">
                                    <a class="dropdown-item" href="holidays">Holidays</a>
                                    <a class="dropdown-item" href="excuses">Excuses</a>
                                    <a class="dropdown-item" href="analysis">Staff Analysis</a>
                                </div>
                            </div>

                        </div>
                        <div class="attendance-filters">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-12">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="attendance-data-table mt-5">
                        <?=
                            $webController->generateDataTable($attendanceDataController->getTableData(), "attendance-data", configurations: [
                                "date_attended" => ['format_date'],
                                "arrival_time" => ['_time_format_to_am_pm'],
                                "departure_time" => ['_time_format_to_am_pm'],
                                "operations" => [
                                    "create" => false,
                                    "edit" => false,
                                    "delete" => false
                                ],
                                'keysToDrop' => ["staff_id"],
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once dirname(__DIR__) . "/includes/footer.php" ?>