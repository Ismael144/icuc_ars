<?php

use App\controllers\StaffDataController;
use App\controllers\NotificationsController;

$pageTitle = "Create Notification";
include dirname(__DIR__) . "/includes/header.php" ?>

<?php
$staffDataController = new StaffDataController;
$notificationsController = new NotificationsController;


// Do some id validations 
if (is_null(get("id"))) {
    $webController->sessionHelper->set(_attendance_holiday_session__error: 'Table Error: An error occured while trying to get record from database');
    $webController->redirect("index");
}

$notificationsId = get('id');

$notificationRecord = $notificationsController->notificationsModel->get($notificationsId);

if (!$notificationRecord) {
    $webController->sessionHelper->set(_attendance_holiday_session__error: 'Table Error: An error occured while trying to get record from database');
    $webController->redirect("index");
}

if ($notificationRecord["user_id"] != $authUser["id"]) {
    $webController->sessionHelper->set(_attendance_holiday_session__notice: 'You cannot make any operations on this notification since you don\'t own it');
    $webController->redirect("index");
}

if (requestMethod('post')) {
    // do validations here 
    $notificationsController->doValidations();

    if ($notificationsController->noErrors()) {
        $notificationsController->update($notificationsId);
        $webController->sessionHelper->set(_notifications_session__success: "Success: You successfully created a new notification..");
        $webController->redirect("index");
    }
}

?>

<?php
include dirname(__DIR__) . "/includes/layouts/topbar.php";
include dirname(__DIR__) . "/includes/layouts/sidebar.php";
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Edit Notification</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= $appDirPath ?>dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Notifications</a></li>
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
                                    <h5 class="card-title mb-3">Viewing Single Notification</h5>
                                    <p class="text-muted">View single notification as selected from the notifications
                                        archive</p>
                                </div>
                                <div class="my-3">
                                    <a href="index" class="d-flex align-items-center gap-2"><i
                                            class="fas fa-arrow-left"></i><span>Go Back</span></a>
                                </div>
                                <div class="col-xxl-12">
                                    <b>
                                        <small>Title</small>
                                    </b>
                                    <h5><?= $notificationRecord['title'] ?></h5>
                                    <b>
                                        <small>Body</small>
                                    </b>
                                    <p>
                                        <?= $notificationRecord['body'] ?>
                                    </p>
                                    <span class="text-muted"><b>
                                            On <?= $notificationsController->format_date($notificationRecord["date_created"]) ?>
                                        </b></span>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->

        </div>
    </div>
</div>

<?php include dirname(__DIR__) . "/includes/footer.php" ?>