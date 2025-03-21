<?php

use App\controllers\AttendanceExcusesController;

$pageTitle = "User Analysis";
include dirname(__DIR__) . "/../includes/header.php";

$attendanceExcusesController = new AttendanceExcusesController;
$excusePaginatedData = $attendanceExcusesController->getExcusesData();
[
  "current_page" => $currentPage,
  "previous_page" => $previousPage,
  "next_page" => $nextPage,
  "total_pages" => $totalPages
] = $excusePaginatedData;
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
            <h4 class="mb-sm-0">Cancel Excuse</h4>

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
                <h5 class="card-title mb-3">Make Excuse</h5>
                <p class="text-muted">View all excuses made by staff members.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>