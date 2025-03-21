<?php

$pageTitle = "Activities";
require_once dirname(__DIR__)."/includes/header.php";

use App\controllers\UserActivitiesController;


if (!isset($_SESSION['uniqid'])) {
    header('location: /icuc_ars/app/auth/signin');
}

$userActivitiesController = new UserActivitiesController;
$recentActivityDates = $userActivitiesController->fetchDateOccured();
$recentUserActivities = $userActivitiesController->fetchActivities();

?>

<!-- Begin page -->
<div id="layout-wrapper">
    <?php include "../includes/layouts/sidebar.php" ?>

    <?php include "../includes/layouts/topbar.php" ?>

    <div class="main-content">
        <div class="page-content mx-4">
            <h4 class="my-4 mb-3">Your Recent Activities</h4>
            <form action="" method="get" class="d-flex align-items-center" style="gap: 5px;">
                <input type="search" name="search" placeholder="Search For Recent Activities ... " class="form-control form-control-md" id="">
                <button class="btn btn-primary btn-sm" style="font-size: 18px;"><i class="bx bx-chevron-right"></i></button>
            </form>
            <div class="date-section">
                <?php foreach (array_values($recentActivityDates) as $key => $date) : ?>
                    <h5 class="my-3">On <?= $webController->format_date($date['datetime_occured']) ?></h5>
                    <div class="row mx-" style="gap: 20px;">
                        <?php foreach (array_values($recentUserActivities) as $key => $activity) : ?>
                            <?php if ($date["datetime_occured"] == $activity["datetime_occured"]) : ?>
                                <div class="recent-activity-card col-sm-3">
                                    <h6 class="mt-0 mb-1 fs-md fw-semibold">
                                        <?= $activity['title'] ?>
                                    </h6>
                                    <p class='text-muted mb-2'>
                                        <?= $activity['body'] ?>
                                    </p>
                                    <span class="text-dark">
                                        <?= $webController->time_format($activity['datetime_occured'], "H:i") ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <form class="app-search d-none d-md-inline-flex">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search..." autocomplete="off" id="search-options" value="">
                                <span class="mdi mdi-magnify search-widget-icon"></span>
                                <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                                <div data-simplebar style="max-height: 320px;">
                                    <!-- item-->
                                    <div class="dropdown-header">
                                        <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                                    </div>
                
                                    <div class="dropdown-item bg-transparent text-wrap">
                                        <a href="index.html" class="btn btn-subtle-secondary btn-sm btn-rounded">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                        <a href="index.html" class="btn btn-subtle-secondary btn-sm btn-rounded">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                                    </div>
                                    <!-- item-->
                                    <div class="dropdown-header mt-2">
                                        <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                                    </div>
                
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                        <span>Analytics Dashboard</span>
                                    </a>
                
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                        <span>Help Center</span>
                                    </a>
                
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                        <span>My account settings</span>
                                    </a>
                
                                    <!-- item-->
                                    <div class="dropdown-header mt-2">
                                        <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                                    </div>
                
                                    <div class="notification-list">
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">Angela Bernier</h6>
                                                    <span class="fs-2xs mb-0 text-muted">Manager</span>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">David Grasso</h6>
                                                    <span class="fs-2xs mb-0 text-muted">Web Designer</span>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-5.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">Mike Bunch</h6>
                                                    <span class="fs-2xs mb-0 text-muted">React Developer</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                
                                <div class="text-center pt-3 pb-1">
                                    <a href="#" class="btn btn-primary btn-sm">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                                </div>
                            </div>
                        </form>

            </div>

        </div>
    </div>
    <script src="../assets/api/Activities.js"></script>

<?php include_once dirname(__DIR__)."/includes/footer.php"; ?>
