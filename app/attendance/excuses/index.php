<?php

use App\controllers\AttendanceExcusesController;

$pageTitle = "User Excuses";
include dirname(__DIR__) . "/../includes/header.php";

$attendanceExcusesController = new AttendanceExcusesController;
$excusePaginatedData = $attendanceExcusesController->getExcusesData();

# Excuses Pagination Setings
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
              <div class="col-xxl-4 mb-3">
                <h5 class="card-title mb-3">Make Excuse</h5>
                <p class="text-muted mb-1 pb-1">View all excuses made by staff members.</p>
                <div class="back-link mb-2">
                  <a href="../" class="">Go Back</a>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <?php if (isAdmin()): ?> 
                  <span>
                    <a href="?id=<?= $authUser['id'] ?>" class="btn btn-primary">View Yours</a>
                  </span>
                  <?php endif; ?>
                  <span>
                    <a href="make" class="btn btn-success">Make Excuse</a>
                  </span>
                </div>

              </div>
              <div class="row">
                <?php if (isAdmin()) : ?>
                  <form action="" method="get" class="mb-3 d-flex align-items-center position-relative">
                    <input type="search" placeholder="Search For Excuses..." name="search" id="" class="form-control" value="<?= get('search') ?>">
                    <span class="submit-button position-absolute" style="right: 20px;">
                      <button class="btn btn-success btn-sm"><i class="fas fa-search"></i></button>
                    </span>
                  </form>
                  <?php if (!is_null(get('search'))) : ?>
                    <div class="mb-2">
                      <a href="index" class="btn btn-sm btn-primary">View All</a>
                    </div>
                  <?php endif; ?>
                <?php endif ?>

                <?php if (!count($excusePaginatedData['data'])): ?>
                  <div class="w-100 d-flex align-items-center justify-content-center" style="height: 280px; border-radius: 5px;">
                    <b>No Excuses Found!</b>
                  </div>
                  <?php endif;  ?>

                <?php foreach ($excusePaginatedData['data'] as $excuse) : ?>
                  <div class="col-6 position-relative">
                    <div class="card border" style="box-shadow: none;">
                      <div class="card-body">
                        <div class="card-title" style="font-weight: bold;">By <?= $excuse["full_name"] ?></div>
                        <div class="reason py-1 px-2 bg-light my-2" style="border-radius: 5px;">
                          <small style="color: rgba(0,0,0,.8); font-weight: bold;">Reason:</small>
                          <p class="card-text mb-0 pb-0"><?= $excuse["reason"] ?></p>
                        </div>
                        <?php
                        $mappedStatus = $attendanceExcusesController->mapExcuseStatus((int)$excuse["status"]);
                        ?>
                        <div class="status my-2 py-1 px-2 bg-light" style="border-radius: 5px;">
                          <small style="color: rgba(0,0,0,.8); font-weight: bold;">For Being:</small>
                          <b class="text-black d-block">
                            <?= $mappedStatus ?>
                          </b>
                        </div>
                        <div class="mt-3 operations" style="position: absolute; top: 0; right: 15px;">
                          <a href="edit?id=<?= $excuse["id"] ?>" style="font-size: 16px;" class="text-primary"><i class="bx bx-pen"></i></a>
                          <a href="delete?id=<?= $excuse["id"] ?>" style="font-size: 16px;" class="text-danger"><i class="bx bx-trash"></i></a>
                        </div>
                        <div class="mt-3" style="font-weight: 600; color: #444;">
                          Made on <?= $attendanceExcusesController->format_date($excuse["date_created"]) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <?php if (count($excusePaginatedData['data'])): ?>
                <?php paginationNavigation($excusePaginatedData) ?>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . "/../includes/footer.php" ?>