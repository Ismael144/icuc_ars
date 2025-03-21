<?php
$pageTitle = "Dashboard Page";
include_once "includes/header.php";

authProtect("auth/signin");

?>

<?php
/** Including The Header */

use App\models\StaffData;
use App\controllers\{UserController, AttendanceDataController};
use App\models\Attendance;

$userController = new UserController;
$attendanceDataController = new AttendanceDataController; 

?>
<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ========== App Menu ========== -->
    <?php include "includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    <?php include "includes/layouts/topbar.php" ?>
</div>

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12 mt-3">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ICUC ARM System</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                                <li class="breadcrumb-item active">ICUC ARM System</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-md">
                    <div class="card overflow-hidden" style="border-left: 5px solid #ffa5008a;">
                        <div class="card-body position-relative">
                            <div class="float-end">
                                <div class="rounded">
                                    <i class="ph ph-user p-3 text-white" style="font-size: 90px; background: #ffa5008a; border-radius: 50%;"></i>
                                </div>
                            </div>
                            <h4>Users</h4>
                            <p class="text-muted mb-4"><?= $userController->userModel->getTableRecordsCount() ?> users in the database</p>
                            <a href="users/index.php" class="d-flex align-items-center gap-3">
                                For more info
                                <i class="ph ph-arrow-right" style="font-size: 18px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card overflow-hidden" style="border-left: 5px solid #f340088a;">
                        <div class="card-body position-relative">
                            <div class="float-end">
                                <div class="rounded">
                                    <i class="ph ph-gear-thin p-3 text-white" style="font-size: 90px; background: #f340088a; border-radius: 50%;"></i>
                                </div>
                            </div>
                            <h4>Staff Data</h4>
                            <p class="text-muted mb-4"><?= (new StaffData)->getTableRecordsCount() ?> data records in the database</p>
                            <a href="" class="d-flex align-items-center gap-3">
                                For more info
                                <i class="ph ph-arrow-right" style="font-size: 18px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card overflow-hidden" style="border-left: 5px solid #0035ff8a;">
                        <div class="card-body position-relative">
                            <div class="float-end">
                                <div class="rounded">
                                    <i class="ph ph-align-left p-3 text-white" style="font-size: 90px; background: #0035ff8a; border-radius: 50%; margin-left: 10px;"></i>
                                </div>
                            </div>
                            <h4>Attendance</h4>
                            <p class="text-muted mb-4"><?= (new Attendance)->getTableRecordsCount() ?> data records in the database</p>
                            <a href="" class="d-flex align-items-center gap-3">
                                For more info
                                <i class="ph ph-arrow-right" style="font-size: 18px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 my-3">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header mb-3">
                        <h4 class="mb-sm-0">Currently Attending (<?= $attendanceDataController->format_date(date('Y-m-d')) ?>)</h4>
                        <p class="my-2">Easily view the staff members who are attending today, with live updating. go to <a href="attendance/index.php">Attendances page</a></p>
                        </div>
                        <div class="card-body pt-1">
                        <div class="row" id="attendance-cards-container">
                            <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                                <div class="spinner-border text-success" style="width: 80px; height: 80px;"></div>
                                <div class="text-dark mt-1" style="font-weight: bold;">Loading Data, Please Wait ...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= $appDirPath ?>assets/js/currentAttendance.js"></script>
    <script>
        const currentAttendanceManager = new AttendanceManager()
    </script>
</div><!--end row-->
</div><!--end row-->
</div><!--end row-->

<?php include_once "includes/footer.php"; ?>