<?php

use App\controllers\AttendanceExcusesController;
use App\controllers\StaffDataController;

$pageTitle = "Make Excuse";
include dirname(__DIR__) . "/../includes/header.php" ?>

<?php
$staffDataController = new StaffDataController;
$attendanceExcusesController = new AttendanceExcusesController;

if (requestMethod('post')) {
    // do validations here 
    $attendanceExcusesController->doValidations();

    if ($attendanceExcusesController->noErrors()) {
        // Checking whether a staff member already has already made an excuse 
        $results = $attendanceExcusesController->checkExcuseForStaffMember($attendanceExcusesController->excuseArray['staff']);

        if ($results) {
            $webController->sessionHelper->set(_attendancemodel__error: 'Excuse Creation: Each staff member can only make one excuse per day...');
            $webController->redirect("index");
        }

        // save the data here
        $attendanceExcusesController->save();
        $webController->redirect("index");
    }
}

?>

<?php
include dirname(__DIR__) . "/../includes/layouts/topbar.php";
include dirname(__DIR__) . "/../includes/layouts/sidebar.php";
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Make Excuse</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Excuses</a></li>
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
                            <div class="row">
                                <div class="col-xxl-4">
                                    <h5 class="card-title mb-3">Make Excuse</h5>
                                    <p class="text-muted">You can create an excuse for either coming late or being absent.</p>
                                    <div class="mb-2">
                                        <a href="index" class="">Go Back</a>
                                    </div>
                                </div>
                                <div class="col-xxl-8">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="excuse_status" class="form-label">Select Status <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status" id="excuse_status">
                                                <option value="">Select Status</option>
                                                <?php foreach ($attendanceExcusesController->excuseStatus as $id => $status) : ?>
                                                    <option value="<?= $id ?>" <?= $id == handleNull("status", $attendanceExcusesController->excuseArray) ? "selected" : "" ?>><?= $status ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger">
                                                <?= $attendanceExcusesController->getFieldErr("status") ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="shortDecs" class="form-label">Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="reason" id="reason" placeholder="Provide a reason here ..." rows="4" cols="5"><?= handleNull("reason", $attendanceExcusesController->excuseArray) ?></textarea>
                                            <span class="text-danger">
                                                <?= $attendanceExcusesController->getFieldErr("reason") ?>
                                            </span>
                                        </div>
                                        <input type="submit" class="btn btn-success" value="Make Excuse">
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

<?php include dirname(__DIR__) . "/../includes/footer.php" ?>