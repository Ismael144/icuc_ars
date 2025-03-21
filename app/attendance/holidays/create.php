<?php

use App\controllers\StaffDataController;
use App\controllers\AttendanceHolidaysController;

$pageTitle = "Create Holiday";
include dirname(__DIR__) . "/../includes/header.php" ?>

<?php
$staffDataController = new StaffDataController;
$attendanceHolidaysController = new AttendanceHolidaysController;

$holidayPostForm = $attendanceHolidaysController->holidayForm;

adminAuthProtect("index");

if (requestMethod('post')) {
    // do validations here 
    $attendanceHolidaysController->doValidations();

    if ($attendanceHolidaysController->noErrors()) {
        // Checking for names 
        $dateByName = $attendanceHolidaysController->attendanceHolidayModel->getRecordsBy("LOWER(name)", strtolower($holidayPostForm['name']), false);

        if (is_array($dateByName)) {
            $attendanceHolidaysController->setFieldError("name", "This name is already taken by another holiday...");
        }

        // Checking whether the entered date is behind the current date and the is_recursive
        // checkbox is marked.
        $formDate = new \DateTime("2024-05-28"); 
        $currentDate = new \DateTime($attendanceHolidaysController->attendanceDataModel->currentDate);

        if ($currentDate > $formDate && $holidayPostForm['is_recursive'] == '1') {
            $attendanceHolidaysController->setFieldError("date", "The date should not be behind the current date except when the 'Is recursive' checkbox is marked...");
        }

        // Check for data duplication e.t.c
        $results = $attendanceHolidaysController->attendanceHolidayModel->getRecordsBy("date", $holidayPostForm['date'], false); 

        if (is_array($results)) {
            $attendanceHolidaysController->setFieldError("date", "A holiday should only be one per day, current holiday on this date is '{$results['name']}'");
        }

        // Check for errors again due to the code above
        if ($attendanceHolidaysController->noErrors()) {
            // save the data here
            $attendanceHolidaysController->save();
            $webController->sessionHelper->set(_attendance_holidays_session__success: 'New Holiday Record: You successfully created a new holiday record.');
            $webController->redirect("index");
        }
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
                        <h4 class="mb-sm-0">Create Holiday</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= $appDirPath ?>dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Holidays</a></li>
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
                                    <h5 class="card-title mb-3">Create Holiday</h5>
                                    
                                </div>
                                <div class="col-xxl-12">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="staff" class="form-label">Holiday's Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="" class="form-control" placeholder="Enter Holiday's Name..." value="<?= handleNull("name", $holidayPostForm) ?>">
                                            <span class="text-danger">
                                                <?= $attendanceHolidaysController->getFieldErr("name") ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="excuse_status" class="form-label">Holiday's Date<span class="text-danger">*</span></label>
                                            <input type="date" value="<?= handleNull("date", $holidayPostForm) ?>" name="date" id="" class="form-control">
                                            <div class="checkbox-group my-2 d-flex align-items-center gap-2 form-check form-check-success">
                                                <input type="checkbox" class="form-check-input" name="is_recursive" id="form-check" <?= empty(handleNull("is_recursive", $holidayPostForm)) ? "" : "checked" ?>>
                                                <label class="text-muted" for="form-check">
                                                    Is recursive (This holiday will be enforced every after a year, meaning it will counted forever)
                                                </label>
                                                
                                            </div>
                                            <span class="text-danger">
                                                <?= $attendanceHolidaysController->getFieldErr("date") ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="shortDecs" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="description" id="description" placeholder="Provide a reason here ..." rows="4" cols="5"><?= handleNull("reason", $holidayPostForm) ?></textarea>
                                            <span class="text-danger">
                                                <?= $attendanceHolidaysController->getFieldErr("description") ?>
                                            </span>
                                        </div>
                                        <input type="submit" class="btn btn-success" value="Create Holiday">
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