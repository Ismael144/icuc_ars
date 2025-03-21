<?php

use App\controllers\notificationsController;
use App\helpers\RequestDataHelper;

$pageTitle = "User Analysis";
include dirname(__DIR__) . "/includes/header.php";

$notificationsController = new NotificationsController;

adminAuthProtect("index");

// Do some id validations 
if (is_null(get("id"))) {
  $webController->sessionHelper->set(_attendance_Notification_session__error: 'Table Error: An error occured while trying to get record from database');
  $webController->redirect("index");
}

$notificationId = get('id');

$notificationsRecord = $notificationsController->notificationsModel->get($notificationId);

if (!$notificationsRecord) {
  $webController->sessionHelper->set(_attendance_Notification_session__error: 'Table Error: An error occured while trying to get record from database');
  $webController->redirect("index");
}

if ($notificationsRecord["user_id"] != $authUser["id"]) {
  $webController->sessionHelper->set(_attendance_holiday_session__notice: 'You cannot make any operations on this notification since you don\'t own it '.$authUser["id"] . $notification["user_id"]);
  $webController->redirect("index");
}

if (requestMethod('post')) {
  $recordName = $notificationsRecord['name'];
  $results = $notificationsController->deleteNotification($notificationId);
  if ($results) {
    $webController->sessionHelper->set(_attendance_Notifications_session__success: "Success: You successfully deleted '$recordName' from notifications ...");
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
            <h4 class="mb-sm-0">Delete Notification</h4>

            <div class="page-title-right">
              <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="index">Attendance</a></li>
                <li class="breadcrumb-item"><a href="all">Notification</a></li>
                <li class="breadcrumb-item active">Delete</li>
              </ol>
            </div>

          </div>
        </div>
      </div>
      <!-- end page title -->
      <div class="row d-flex align-items-center justify-content-center">
        <div class="col-4">
          <form action="" method="POST">
            <div class="card text-start">
              <div class="card-body">
                <h4 class="card-title"><?= $notificationsRecord['title'] ?></h4>
                <p class="card-text"><?= $notificationsRecord['body'] ?></p>
                <div class="op-buttons">
                  <button class="btn-danger btn btn-sm">Delete</button>
                  <a href="index" class="btn btn-sm btn-success">Go Back</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>