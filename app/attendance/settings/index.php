<?php
$pageTitle = "Settings Page";
include dirname(__DIR__)."/../includes/header.php";

authProtect("auth/signin");

use App\controllers\UserController;
use App\controllers\WebController;
use App\controllers\JSONSettingsController; 

$JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__))."/../settings/attendance_settings.json");
$attendanceTimeSettings = $JSONSettingsController->getSetting("attendanceTime"); 

$assetsPath = "";
$userController = new UserController;
$websiteController = new WebController;

$freeDiskSpace = function ($withUnits = false) use ($websiteController) {
    return $websiteController->displayStorage($withUnits)["free_space"];
};
$totalDiskSpace = function ($withUnits = false) use ($websiteController) {
    return $websiteController->displayStorage($withUnits)["total_space"];
};

include dirname(__DIR__)."/../includes/layouts/sidebar.php";
include dirname(__DIR__)."/../includes/layouts/topbar.php";
?>

<div class="main-content">
    <div class="page-content">
        <div class="container">

            <div class="row justify-content-center">

                <div class="col-xl-10 mt-3">

                    <div class="row">

                        <div class="col-xl-9">

                            <div id="general" class="mb-5">
                                <h4><i class="far fa-clock fa-fw text-body text-opacity-50 me-1"></i> Attendance Time</h4>
                                <p>View and update your general account information and settings.</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Arrival Time</div>
                                                <div class="text-body text-opacity-50"><?= $webController->_time_format_to_am_pm($attendanceTimeSettings["arrival_time"]) ?></div>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Departure Time</div>
                                                <div class="text-body text-opacity-50"><?= $webController->_time_format_to_am_pm($attendanceTimeSettings["departure_time"]) ?></div>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Close Time</div>
                                                <div class="text-body text-opacity-50"><?= $webController->_time_format_to_am_pm($attendanceTimeSettings["close_time"]) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isAdmin()): ?>
                                <div class="btn-edit">
                                    <a href="edit" class="btn btn-outline-success">Edit</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>


                        <!-- <div class="col-xl-3">

                            <nav id="sidebar-bootstrap" class="navbar navbar-sticky d-none d-xl-block sticky-top settings-side-nav" style="top: 70px !important; z-index: 1 !important;">
                                <style>
                                    .settings-side-nav .nav-link {
                                        padding-top: 4px !important;
                                        padding-bottom: 4px !important;
                                    }
                                </style>
                                <nav class="nav d-flex flex-column" style="font-size: 15px;">
                                    <a class="nav-link active" href="#general" data-toggle="scroll-to">General</a>
                                    <a class="nav-link" href="#notifications" data-bs-toggle="scroll-to">Notifications</a>
                                    <a class="nav-link" href="#privacyAndSecurity" data-bs-toggle="scroll-to">Privacy and security</a>
                                    <a class="nav-link" href="#payment" data-bs-toggle="scroll-to">Payment</a>
                                    <a class="nav-link" href="#shipping" data-bs-toggle="scroll-to">Shipping</a>
                                    <a class="nav-link" href="#mediaAndFiles" data-bs-toggle="scroll-to">Media and Files</a>
                                    <a class="nav-link" href="#languages" data-bs-toggle="scroll-to">Languages</a>
                                    <a class="nav-link" href="#system" data-bs-toggle="scroll-to">System</a>
                                    <a class="nav-link" href="#resetSettings" data-bs-toggle="scroll-to">Reset settings</a>
                                </nav>
                            </nav>

                        </div> -->

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<?php include_once dirname(__DIR__)."/../includes/footer.php"; ?>
