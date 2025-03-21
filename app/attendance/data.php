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
use App\controllers\UserController;
use App\controllers\UserDepartmentController;

$userController = new UserController;
$staffDataController = new StaffDataController;
$userDeptController = new UserDepartmentController;
$attendanceDataController = new AttendanceDataController;

$JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__)) . "/settings/attendance_settings.json");
$attendanceTime = $JSONSettingsController->getSetting("attendanceTime");

$attendanceTableData = $attendanceDataController->getTableData();


// $webController->generateDataTable($attendanceTableData, "Attendance Data");

$staffMember = get('staff');
$department = get('department');
$dateFilter = get('date');

// Construct the query 
$filterOptions = array_filter(['staff_data_id' => $staffMember, 'date_attended' => $dateFilter, 'some_value' => NULL], function ($item) {
    return !is_null($item);
});

$filters = [];
$tableData = [];
$whereClauseString = "WHERE 1";

if (!is_null(get('staff'))) {
    if (strlen(get('staff'))) {
        $filters['user_id'] = get('staff');
        $whereClauseString .= " AND user_id = :user_id";
    }
}

if (!is_null(get('date'))) {
    if (strlen(get('date'))) {
        $filters['date_attended'] = get('date');
        $whereClauseString .= " AND date_attended = :date_attended";
    }
}

if (!is_null(get('dept'))) {
    if (strlen(get('dept'))) {
        $filters['dept'] = get('dept');
        $whereClauseString .= " AND u.dept_id = :dept";
    }
}

$query = <<<"SQL"
    SELECT a.id as id, concat(s.first_name, ' ', s.last_name) as full_name, u.email, u.dept_id, a.arrival_time, a.departure_time, a.date_attended FROM attendance AS a JOIN staff_data AS s ON a.staff_data_id = s.id JOIN users AS u ON s.user_id = u.id $whereClauseString ORDER BY a.staff_data_id;
SQL;

$filteredData = $attendanceDataController->attendanceDataModel->runPreparedQuery($query, $filters, true, true);

// This variable is for serial number incremention

$attendanceTableData = array_map(function ($item) use ($attendanceDataController, $attendanceTime, $userDeptController) {
    // Knowing How late the staff member was
    $fixedTime = $attendanceDataController->time_format($item["arrival_time"], "H:i");
    $elapsed = $attendanceDataController->attendanceDataModel->getTimeElapsed($fixedTime, $attendanceTime["arrival_time"]);

    if ($elapsed['hours'] < 0 || $elapsed['minutes'] < 1) {
        $item["time_late"] = "Arrived In Time";
    } else {
        if ($elapsed['hours'] > 0) {
            if ($elapsed['minutes'] > 0) {
                $item["time_late"] = "{$elapsed['hours']} hours and {$elapsed['minutes']} minutes";
            } else {
                $item["time_late"] = "{$elapsed['hours']} hours";
            }
        } else {
            $item["time_late"] = "{$elapsed['minutes']} minutes";
        }
    }

    $item["serial_no"] = $item["id"];
    $item["department"] = !is_null($item["dept_id"]) ? $userDeptController->userDeptModel->getRecordsBy('id', $item['dept_id'])['name'] : "Not Assigned";
    $item["arrival_time"] = $attendanceDataController->_time_format_to_am_pm($item["arrival_time"]);
    $item["departure_time"] = $attendanceDataController->_time_format_to_am_pm($item["departure_time"]);
    $item["date_attended"] = $attendanceDataController->format_date($item["departure_time"]);

    return array_merge($item);
}, $filteredData);

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
                                <li class="breadcrumb-item active">Attendance Archive</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intro-banner w-100 d-flex align-items-center justify-content-center flex-column bg-light mb-4" style="height: 250px; border-radius: 10px;">
                <img src="../assets/images/logo.png" alt="" width="100px" height="100px">
                <h4 class="my-2">Attendance Data Archive</h4>
                <p>This is all attendance data of all staff members since this system started operating.</p>
            </div>
            <div class="card">
                <div class="card-body pt-1">
                    <div class="attendance-data-table mt-4">
                        <form action="" method="get" class="d-flex align-items-center justify-content-between">
                            <div></div>
                            <div class="filters d-flex align-items-center gap-2" style="width: 460px;">
                                <select name="dept" id="" class="form-select-sm form-select">
                                    <option value="">Select Department</option>
                                    <?php foreach ($userDeptController->userDeptModel->fetchAllData() as $department) : ?>
                                        <option value="<?= $department['id'] ?>" <?= $department['id'] == get('dept') ? "selected" : "" ?>><?= $department['name'] ?></option>
                                    <?php endforeach;  ?>
                                </select>
                                <select name="staff" id="" class="form-select-sm form-select" style="width: 420px;">
                                    <option value="">Select Staff Member</option>
                                    <?php foreach ($staffDataController->staffDataModel->fetchAllData() as $staffUser) : ?>
                                        <option value="<?= $staffUser["user_id"] ?>" <?= $staffUser['user_id'] == get('staff') ? "selected" : "" ?>><?= $staffUser['first_name'] . ' ' . $staffUser['last_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <input type="date" name="date" value="<?= get('date') ?>" class="form-control-sm form-control" style="width: 450px;">
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                        <div class="data-archive-table my-3">
                            <div class="table-responsive">
                                <table class="display table table-bordered table-striped align-middle mt-2" id="attendance-archive-datatable">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Full Name</th>
                                            <th>Email Address</th>
                                            <th>Department</th>
                                            <th>C. In Time</th>
                                            <th>C. Out Time</th>
                                            <th>Time Late</th>
                                            <th>Date Attended</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php foreach ($attendanceTableData as $attendanceRecord) : ?>
                                            <tr>
                                                <td scope="row"><?= $attendanceRecord["serial_no"] ?></td>
                                                <td><?= $attendanceRecord["full_name"] ?></td>
                                                <td rowspan="1"><?= $attendanceRecord["email"] ?></td>
                                                <td><?= $attendanceRecord["department"] ?></td>
                                                <td><?= $attendanceRecord["arrival_time"] ?></td>
                                                <td><?= $attendanceRecord["departure_time"] ?></td>
                                                <td><?= $attendanceRecord["time_late"] ?></td>
                                                <td><?= $attendanceRecord["date_attended"] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <script>
                                    // new DataTable('#$tableName-datatable');
                                    var a = $("#attendance-archive-datatable").DataTable({
                                        language: {
                                            paginate: {
                                                previous: "<i class='mdi mdi-chevron-left'>",
                                                next: "<i class='mdi mdi-chevron-right'>"
                                            }
                                        },
                                        drawCallback: function() {
                                            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once dirname(__DIR__) . "/includes/footer.php" ?>