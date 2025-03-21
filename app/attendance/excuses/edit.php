<?php

use App\controllers\AttendanceExcusesController;

$pageTitle = "User Analysis";
include dirname(__DIR__) . "/../includes/header.php";

$attendanceExcusesController = new AttendanceExcusesController;
$excusePostData = $attendanceExcusesController->excuseArray;

$excuseId = get('id');

if (is_null(get("id"))) {
    $webController->sessionHelper->set(_attendance_excuse_session__error: "Edit Excuse: An Error Occured while trying to edit the excuse");
    $webController->redirect("index");
}

$staffMemberExcuse = $attendanceExcusesController->getSingleExcuse((int)$excuseId);

if ($staffMemberExcuse == false) {
    $webController->sessionHelper->set(_attendance_excuse_session__error: "Edit Excuse: An Error Occured while trying to edit the excuse");
    $webController->redirect("index");
}

$excuseStatus = empty(handleNull("status", $excusePostData)) ? $staffMemberExcuse["status"] : handleNull("status", $excusePostData);
$excuseReason = empty(handleNull("reason", $excusePostData)) ? $staffMemberExcuse["reason"] : handleNull("reason", $excusePostData);

if (requestMethod('post')) {

    // Do some validations here
    if (empty($excuseStatus)) {
        $attendanceExcusesController->setFieldError("status", "This field is required");
    }

    if (empty($excuseReason)) {
        $attendanceExcusesController->setFieldError("reason", "This field is required, $reason");
    } else {
        if (str_word_count($excuseReason) < 5) {
            $attendanceExcusesController->setFieldError("reason", "Your reason should be atleast 5 words");
        }
    }

    if ($attendanceExcusesController->noErrors()) {
        $attendanceExcusesController->updateExcuse($excuseId);
        $webController->sessionHelper->set(_attendance_excuse_session__success: 'Excuse Edit: You successfully edited the excuse for \'' . $staffMemberExcuse["full_name"] . '\'');
        $webController->redirect("index");
    } else {
        $webController->sessionHelper->set(_attendance_excuse_session__info: 'Excuse Edit: Deal with all errors first in order to proceed. ');
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
                                <li class="breadcrumb-item"><a href="all">Excuses</a></li>
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
                            <div class="col-xxl-4 mb-3">
                                <h5 class="card-title mb-3">Edit Excuse</h5>
                                <p class="text-muted">Edit excuses made by staff members here.</p>
                                <div class="mb-2">
                                    <a href="index" class="">Go Back</a>
                                </div>
                                <div class="mt-2 mb-3">
                                    <span class="" style="font-weight: bold;">Editing Excuse for '<?= $staffMemberExcuse['full_name'] ?>'</span>
                                </div>
                                <div class="col-xxl-8">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="excuse_status" class="form-label">Select Status <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status" id="excuse_status">
                                                <option value="">Select Status</option>
                                                <?php foreach ($attendanceExcusesController->excuseStatus as $id => $status) : ?>
                                                    <option value="<?= $id ?>" <?= $id == $excuseStatus ? "selected" : "" ?>><?= $status ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger">
                                                <?= $attendanceExcusesController->getFieldErr("status") ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="reason" id="reason" placeholder="Provide a reason here ..." rows="4" cols="5"><?= $excuseReason ?></textarea>
                                            <span class="text-danger">
                                                <?= $attendanceExcusesController->getFieldErr("reason") ?>
                                            </span>
                                        </div>
                                        <input type="submit" class="btn btn-success" value="Make Excuse">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . "/../includes/footer.php" ?>