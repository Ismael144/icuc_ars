<?php

use App\controllers\StaffDataController;

$pageTitle = "Attendance Checkpoint Page";
include_once "../includes/header.php";

authProtect("../auth/signin");

$staffDataController = new StaffDataController();

$staffData = $staffDataController->staffDataModel->getRecordsBy("user_id", $authUser['id']);

if ($staffData == false) {
    $id = $authUser["id"];
    $staffData = $staffDataController->staffDataModel->getRecordsBy("user_id", $id);
    if ($staffData == false) session_redirect("../staff_data/create?id=$id", ["_staff_data__info" => " One Step Close: We need just a couple of information inorder to continue, help us fill in below."]);
}

?>

<?php
/** Including The Header */

use App\controllers\{UserController, AttendanceDataController};

$userController = new UserController;
$attendanceDataController = new AttendanceDataController;

$attendanceRecord = $attendanceDataController->checkAttendanceExistence(["staff_data_id" => $staffData['id'], "date_attended" => date('Y-m-d')], true);

[$isCheckInTime, $isCheckOutTime] = [$attendanceDataController->isCheckinOrCheckoutTime()['check_in'], $attendanceDataController->isCheckinOrCheckoutTime()['check_out']];

$attendanceSettings = $attendanceDataController->getAttendanceSettings();

if (isset($attendanceRecord['arrival_time'])) {
    $fixedTime = $attendanceDataController->time_format($attendanceRecord["arrival_time"], "H:i");
    $elapsed = $attendanceDataController->attendanceDataModel->getTimeElapsed($fixedTime, $attendanceSettings['arrival_time']);

    $attendanceRecord["time_late"] = $attendanceDataController->interpreteTimeLate($elapsed);
}
?>
<!-- Begin page -->
<div id="layout-wrapper">
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>
    <!-- ========== App Menu ========== -->
    <?php include "../includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <?php include "../includes/layouts/topbar.php" ?>
</div>


<div class="main-content">
    <div class="page-content d-flex align-attendanceRecords-center justify-content-center mt-3">
        <div class="card" style="width: 27rem;">
            <div class="card-body text-center position-relative">
                <div id="loader" class="text-left d-flex align-attendanceRecords-center gap-2" style="position: absolute; top: 10px; right: 15px;"></div>
                <button class="btn btn-lg btn-success" id="check-button" style="width: 200px; height: 200px; margin: 10px; border-radius: 50%; border: 6px solid #eee; box-shadow: 1px 2px 5px 0 rgba(0, 0, 0, .2)" <?= $isCheckInTime && count($attendanceRecord) || $isCheckOutTime && strlen(handleNull('departure_time', $attendanceRecord)) || $isCheckOutTime && !count($attendanceRecord) || !$isCheckInTime && !$isCheckOutTime ? "title='Attendance Checked!' disabled" : "onclick='cd3227e7e1697dc016124ed1714ab()'" ?>>
                    <?php if ($isCheckInTime) : ?>
                        <?php if (count($attendanceRecord)) : ?>
                            Checked In
                        <?php else : ?>
                            Check In
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($isCheckOutTime) : ?>
                        <?php if (count($attendanceRecord) && !is_null($attendanceRecord['departure_time'])) : ?>
                            Checked Out
                        <?php else : ?>
                            <?php if ($isCheckOutTime && !count($attendanceRecord)) : ?>
                                Not Checked In
                                <?php alert(_attendance_check__info: "Attendance Check: You did not check in, so you can't check out") ?>
                            <?php else : ?>
                                Check Out
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!$isCheckInTime && !$isCheckOutTime) : ?>
                        Check Off
                    <?php endif ?>
                </button>
                <div>
                    <div class="status-bar d-flex align-items-center justify-content-between">
                        <div id="internet-status"></div>
                        <a href="" class="btn btn-light btn-sm">Refresh</a>
                    </div>
                    <div class="row text-left attendance-content my-2 bg-light p-3 border" style="border-radius: 5px; text-align: left; margin-right: 0px; margin-left: 0px;">
                        <div class="col">
                            <div class="group my-1">
                                <small>Mode</small>
                                <b class="card-text d-block">
                                    <?= $isCheckInTime ? "Check In" : "Check Out" ?>
                                </b>
                            </div>
                            <div class="group my-1">
                                <small>Staff</small>
                                <b class="card-text d-block">
                                    <?= $staffData['first_name'] . ' ' . $staffData['last_name'] ?>
                                </b>
                            </div>
                            <div class="group my-1">
                                <small>Check In Time</small>
                                <b class="card-text d-block" id="check_in">
                                    <?= $attendanceDataController->_time_format_to_am_pm($attendanceSettings['arrival_time']) ?>
                                </b>
                            </div>
                            <div class="group my-1">
                                <small>Check Out Time</small>
                                <b class="card-text d-block" id="check_out">
                                    <?= $attendanceDataController->_time_format_to_am_pm($attendanceSettings['departure_time']) ?>
                                </b>
                            </div>
                        </div>
                        <div class="col">
                            <div class="group my-1">
                                <small>Checked In</small>
                                <b id="check_in_time" class="d-block">
                                    <?= count($attendanceRecord) ? $attendanceDataController->_time_format_to_am_pm($attendanceRecord['arrival_time']) : "--:--" ?>
                                </b>
                            </div>
                            <div class="group my-1">
                                <small>Checked Out</small>
                                <b id="check_out_time" class="d-block">
                                    <?= isset($attendanceRecord['departure_time']) ? $attendanceDataController->_time_format_to_am_pm($attendanceRecord['departure_time']) : "--:--" ?>
                                </b>
                            </div>

                            <div class="group my-1">
                                <small>Time Late</small>
                                <b id="time_late" class="d-block">
                                    <?= isset($attendanceRecord['time_late']) ? $attendanceRecord['time_late'] : "--:--" ?>
                                </b>
                            </div>
                            <div class="group my-1">
                                <small>Checked</small>
                                <b class="card-text d-block" id="attendance-status">
                                    <?= count($attendanceRecord) ? "Yes" : "No" ?>
                                </b>
                            </div>
                        </div>
                    </div>
                    <div id="status-report"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // 8
        const e44f0089b076e18a718eb9ca3d94674 = <?= $staffData['id']; ?>
    </script>
    <script src="../assets/js/d85fbdd12e746114f2133428b8544723.js"></script>
</div>

<?php include_once "../includes/footer.php"; ?>