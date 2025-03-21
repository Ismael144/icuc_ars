<?php

use App\controllers\StaffDataController;
use App\controllers\AttendanceHolidaysController;

$pageTitle = "Edit Holiday";
include dirname(__DIR__) . "/../includes/header.php" ?>

<?php
$staffDataController = new StaffDataController;
$attendanceHolidaysController = new AttendanceHolidaysController;

adminAuthProtect("index");

// Do some id validations 
if (is_null(get("id"))) {
    $webController->sessionHelper->set(_attendance_holiday_session__error: 'Table Error: An error occured while trying to get record from database');
    $webController->redirect("index");
}

$holidayId = get('id');

$holidayForm = $attendanceHolidaysController->holidayForm;
$holidayRecord = $attendanceHolidaysController->attendanceHolidayModel->get($holidayId);

if (!$holidayRecord) {
    $webController->sessionHelper->set(_attendance_holiday_session__error: 'Table Error: An error occured while trying to get record from database');
    $webController->redirect("index");
}

$holidayName = empty(handleNull("name", $holidayForm)) ? $holidayRecord['name'] : handleNull("name", $holidayForm);

$holidayDate = empty(handleNull("date", $holidayForm)) ? $holidayRecord['date'] : handleNull("date", $holidayForm);

// Checking the box when the holiday recursive field is 2
$dateIsRecursive = empty(handleNull("is_recursive", $holidayForm)) ? (
    $holidayRecord['is_recursive'] == 2 ? "checked" : ""
) : handleNull("date", $holidayForm);

$holidayDescription = empty(handleNull("description", $holidayForm)) ? $holidayRecord['description'] : handleNull("description", $holidayForm);

$holidayPostForm = $attendanceHolidaysController->holidayForm;

if (requestMethod('post')) {
    // do validations here 
    $attendanceHolidaysController->doValidations();

    if ($attendanceHolidaysController->noErrors()) {
        // Checking if there another name that resembles the one entered by the user 
        $dateByName = $attendanceHolidaysController->attendanceHolidayModel->getRecordsBy("LOWER(name)", strtolower($holidayPostForm['name']), false);

        if (is_array($dateByName) && $holidayForm['name'] != $holidayRecord['name']) {
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

        if (is_array($results) && $holidayForm['date'] != $holidayRecord['date']) {
            $attendanceHolidaysController->setFieldError("date", "A holiday should only be one per day, current holiday on this date is '{$results['name']}'");
        }

        // Check for errors again due to the code above
        if ($attendanceHolidaysController->noErrors()) {
            // save the data here
            $results = $attendanceHolidaysController->update($holidayId);

            if ($results) {
                $webController->sessionHelper->set(_attendance_holidays_session__success: 'Update Successful: You successfully updated this record');
                $webController->redirect("index");
            }
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
                        <h4 class="mb-sm-0">Edit Holiday</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= $appDirPath ?>dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Holidays</a></li>
                                <li class="breadcrumb-item active">Edit</li>
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
                                    <h5 class="card-title mb-3">Edit Holiday</h5>
                                    
                                </div>
                                <div class="col-xxl-12">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="holiday_name" class="form-label">Holiday's Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="" class="form-control" placeholder="Enter Holiday's Name..." value="<?= $holidayName ?>" id="holiday_name">
                                            <span class="text-danger">
                                                <?= $attendanceHolidaysController->getFieldErr("name") ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="holiday_date" class="form-label">Holiday's Date<span class="text-danger">*</span></label>
                                            <input type="date" value="<?= $holidayDate ?>" name="date" id="holiday_date" class="form-control">
                                            <div class="checkbox-group my-2 d-flex align-items-center gap-2 form-check form-check-success">
                                                <input type="checkbox" class="form-check-input" name="is_recursive" id="form-check" <?= $dateIsRecursive ?>>
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
                                            <textarea class="form-control" name="description" id="description" placeholder="Provide a reason here ..." rows="4" cols="5"><?= $holidayDescription ?></textarea>
                                            <span class="text-danger">
                                                <?= $attendanceHolidaysController->getFieldErr("description") ?>
                                            </span>
                                        </div>
                                        <input type="submit" class="btn btn-success" value="Edit Holiday">
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