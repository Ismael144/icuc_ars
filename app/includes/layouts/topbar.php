<?php

use App\controllers\NotificationsController;
use App\controllers\UserActivitiesController;

if (session_id() == "") {
    session_start();
}

$activitiesController = new UserActivitiesController;

$authUserAvatar = "/icuc_ars/images/users/{$authUser['avatar']}";

$notificationsController = new NotificationsController;

$latestNotifications = $notificationsController->latestNotifications();

authProtect("{$appDirPath}auth/signin");

?>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="navbar-items-group d-flex align-items-center">
                <div class="dropdown open">
                    <button class="mx-2 text-muted btn btn-light dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu mt-3" aria-labelledby="triggerId">
                        <div class="container-fluid">
                            <ul class="navbar-nav mx-4 py-2" id="navbar-nav" style="overflow: auto !important; width: 260px !important;">
                                <li class="nav-item">
                                    <a class="nav-link menu-link collapsed" href="/icuc_ars/app/dashboard">
                                        <i class="ph-gauge"></i> <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <?php if (isAdmin()) : ?>
                                    <li class="nav-item">
                                        <a href="/icuc_ars/app/users/index" class="nav-link menu-link"> <i class="ph-user"></i> <span data-key="t-calendar">Users</span> </a>
                                    </li>
                                    <div class="dropdown-divider"></div>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <?php
                                    // For the link 
                                    $link = isAdmin() ? "/icuc_ars/app/staff_data/index" : "/icuc_ars/app/staff_data/images/index?id=" . $authUser['id'];
                                    ?>
                                    <a href="<?= $link ?>" class="nav-link menu-link"> <i class="ph-chats"></i> <span data-key="t-chat">
                                            <?php if (!isAdmin()) : ?>
                                                Your Data
                                            <?php else : ?>
                                                Staff Data
                                            <?php endif; ?>
                                        </span></a>
                                </li>
                                <div class="dropdown-divider"></div>

                                <li class="nav-item">
                                    <a href="/icuc_ars/app/attendance/index " class="nav-link menu-link"> <i class="ph-align-left"></i> <span data-key="t-attendance">Attendance</span> </a>
                                </li>
                                <li class="nav-item">
                                    <div class="dropdown-divider"></div>
                                    <a href="/icuc_ars/app/attendance/holidays/index " class="nav-link "> <i class="ph-calendar"></i> <span data-key="t-holidays">Holidays</span> </a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <li class="nav-item">
                                    <a href="/icuc_ars/app/departments/index " class="nav-link "> <i class="ph-house"></i> <span data-key="t-holidays">Departments</span> </a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <li class="nav-item">
                                    <a class="nav-link menu-link collapsed" href="/icuc_ars/app/notifications">
                                        <i class="ph-chat"></i> <span data-key="t-dashboards">Notifications</span>
                                    </a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <li class="nav-item">
                                    <a class="nav-link menu-link collapsed" href="/icuc_ars/app/attendance_check">
                                        <i class="ph-clock"></i> <span data-key="t-dashboards">Attendance Check</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="dropdown open">
                    <a class="btn btn-success dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Access
                    </a>
                    <div class="dropdown-menu my-3" aria-labelledby="triggerId">
                        <?php if (isAdmin()) : ?>
                            <h6 class="dropdown-header">Data Creation</h6>
                            <a class="dropdown-item" href="<?= $appDirPath ?>users/create">Create New User</a>
                            <a class="dropdown-item" href="<?= $appDirPath ?>staff_data/create">Create New Staff Member</a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        <h6 class="dropdown-header">Attendance</h6>
                        <a class="dropdown-item" href="<?= $appDirPath ?>attendance/index">Currently Attending</a>
                        <a class="dropdown-item" href="<?= $appDirPath ?>attendance/analysis">Attendance Analysis</a>
                    </div>
                </div>
            </div>


            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="http://localhost/icuc_ars/app/assets/images/logo.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="http://localhost/icuc_ars/app/assets/images/logo.png" alt="" height="22">
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="http://localhost/icuc_ars/app/assets/images/logo.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="http://localhost/icuc_ars/app/assets/images/logo.png" alt="" height="22">
                        </span>
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <i class='mdi mdi-bell-outline fs-2xl'></i>
                            <?php if ($latestNotifications['notifications_count'] > 0) : ?>
                                <span class="position-absolute topbar-badge fs-3xs translate-middle badge rounded-pill bg-danger"><span class="notification-badge"><?= $latestNotifications["notifications_count"] ?></span><span class="visually-hidden">unread messages</span></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                            <div class="dropdown-head rounded-top">
                                <div class="p-3 border-bottom border-bottom-dashed">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="mb-0 fs-lg fw-semibold d-flex align-items-center justify-content-between"> Notifications <span class="badge bg-danger-subtle text-danger fs-sm notification-badge">
                                                    <?= $latestNotifications["notifications_count"] ?></span></h6>
                                            <?php if ($latestNotifications['notifications_count'] > 0) : ?>
                                                <p class="fs-md text-muted mt-1 mb-0">You have <span class="fw-semibold notification-unread"><?= $latestNotifications["notifications_count"] ?></span>
                                                    unread messages</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="py-2 ps-2" id="notificationItemsTabContent">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php foreach ($latestNotifications["latest_notifications"] as $notification) : ?>
                                        <div class="text-reset notification-item py-3 d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="position-relative me-3 flex-shrink-0">
                                                    <span class="active-badge position-absolute start-100 translate-middle p-1 bg-warning rounded-circle">
                                                        <span class="visually-hidden">New alerts</span>
                                                    </span>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-md fw-semibold">
                                                            <?= $notification["title"] ?>
                                                        </h6>
                                                    </a>
                                                    <div class="fs-sm text-muted">
                                                        <p class="mb-1"><?= $notification["body"] ?></p>
                                                    </div>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            <?= $notificationsController->time_ago_string($notification["date_created"]) ?></span>
                                                    </p>
                                                    <span>
                                                        By <?php
                                                            if ($notification["user_id"] == $authUser["id"]) {
                                                                echo "You";
                                                            } else {
                                                                echo $notification["user"]["username"];
                                                            }

                                                            ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                    <?php if (!count($latestNotifications["latest_notifications"])) : ?>
                                        <div class="pb-5 d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <b class="text-muted d-flex align-items-center justify-content-center gap-2 flex-column">
                                                <span>
                                                    <i class="bx bx-chat" style="font-size: 50px;"></i>
                                                </span>
                                                <span>
                                                    No Notifications
                                                </span>
                                            </b>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="notification-actions" id="notification-actions">
                                    <div class="d-flex text-muted justify-content-center align-items-center">
                                        Select <div id="select-content" class="text-body fw-semibold px-1">0</div>
                                        Result <button type="button" class="btn btn-link link-danger p-0 ms-2" data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center p-2 px-3" style="background: rgba(0, 0, 0, .05); border-radius: 15px;">
                                <?php
                                $imgPath = !strlen($authUser['avatar']) ? "{$appDirPath}assets/images/male-img.jpg" : "/icuc_ars/images/users/{$authUser['avatar']}"
                                ?>
                                <img class="rounded-circle header-profile-user" src="<?= $imgPath ?>" alt="Header Avatar">

                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?= ucwords($authUser['username']) ?></span>
                                    <span class="d-none d-xl-block ms-1 fs-sm user-name-sub-text"><?= $authUser['role'] ?></span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="/icuc_ars/app/auth/profile/user"><i class="mdi mdi-account-circle text-muted fs-lg align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/icuc_ars/app/auth/signout"><i class="mdi mdi-logout text-muted fs-lg align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Sign Out</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</header>