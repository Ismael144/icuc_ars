<?php
$pageTitle = "Settings Page";
include dirname(__DIR__) . "/../includes/header.php";

authProtect("auth/signin");

adminAuthProtect("index");

use App\controllers\UserController;
use App\controllers\WebController;
use App\controllers\JSONSettingsController;

$JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__)) . "/../settings/attendance_settings.json");
$attendanceTimeSettings = $JSONSettingsController->getSetting("attendanceTime");

$userController = new UserController;
$websiteController = new WebController;


include dirname(__DIR__) . "/../includes/layouts/sidebar.php";
include dirname(__DIR__) . "/../includes/layouts/topbar.php";

$fieldErrors = [];

$arrivalTime = $webController->sessionHelper->filter(handleNull("arrival_time", $_POST));
$departureTime = $webController->sessionHelper->filter(handleNull("departure_time", $_POST));

if (requestMethod('post')) {
    // Do form validation 
    if (empty($arrivalTime)) {
        $fieldErrors["arrival_time"] = "This field is required...";
    }

    if (empty($departureTime)) {
        $fieldErrors["departure_time"] = "This field is required...";
    }

    if (empty($fieldErrors)) {
        // Do the updates
        echo "<script>alert('Hello world')</script>";
        $JSONSettingsController = new JSONSettingsController(dirname(dirname(__DIR__)) . "/../settings/attendance_settings.json");
        $JSONSettingsController->writeSettings(["attendanceTime" => ["arrival_time" => $arrivalTime, "departure_time" => $departureTime, "closeTime" => "22:00"]]);
    }
}

?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Edit Time</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= $appDirPath ?>dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Times</a></li>
                                <li class="breadcrumb-item active">Make</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="">
                                <div class="col-xxl-4">
                                    <h5 class="card-title mb-3">Edit Time</h5>
                                    
                                </div>
                                <div class="col-xxl-12">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="staff" class="form-label">Arrival Time <span class="text-danger">*</span></label>
                                            <input type="time" name="arrival_time" value="<?= handleNull("arrival_time", $attendanceTimeSettings) ?>" id="" class="form-control" placeholder="Enter Time's Name...">
                                            <span class="text-danger">
                                                <?= handleNull("arrival_time", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="staff" class="form-label">Departure Time <span class="text-danger">*</span></label>
                                            <input type="time" name="departure_time" value="<?= handleNull("departure_time", $attendanceTimeSettings) ?>" id="" class="form-control" placeholder="Enter Time's Name...">
                                            <span class="text-danger">
                                            <?= handleNull("departure_time", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <input type="submit" class="btn btn-success" value="Edit Time">
                                    </form>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->

        </div>
    </div>
</div>

<?php include_once dirname(__DIR__) . "/../includes/footer.php"; ?>