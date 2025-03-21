<?php
$pageTitle = "All Notifications";
include dirname(__DIR__) . "/includes/header.php"
    ?>

<?php

use App\controllers\NotificationsController;

include dirname(__DIR__) . "/includes/layouts/topbar.php";
include dirname(__DIR__) . "/includes/layouts/sidebar.php";

$notificationsController = new NotificationsController;

?>

<div class="main-content">
    <style>
        .notification:hover {
            background: rgba(0, 0, 0, .05);
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">View Notifications</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= $appDirPath ?>dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item"><a href="index">Notifications</a></li>
                                <li class="breadcrumb-item active">All</li>
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
                                <div class="">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="card-title mb-3">Notifications</h5>
                                        <div class="dropdown open">
                                            <button class="btn btn-success dropdown-toggle" type="button" id="triggerId"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                More
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="triggerId">
                                                <a class="dropdown-item" href="create">Create Notification</a>
                                            </div>
                                        </div>

                                    </div>
                                    <p class="text-muted">View all notifications created by all system administrators
                                        and you can search for all notifications you want</p>
                                </div>
                                <div class="col-xxl-12">
                                    <form action="" method="get"
                                        class="mb-2 d-flex align-items-center position-relative">
                                        <input type="search" placeholder="Search For Notifications..." name="search"
                                            id="" class="form-control" value="<?= get('search') ?>">
                                        <span class="submit-button position-absolute" style="right: 10px;">
                                            <button class="btn btn-success btn-sm"><i
                                                    class="fas fa-search"></i></button>
                                        </span>
                                    </form>

                                    <?php if (!is_null(get('search'))): ?>
                                        <a href="index" class="btn btn-sm btn-primary">View All</a>

                                    <?php endif; ?>

                                    <?php if (!count($notificationsController->getAll()) && !is_null(get('search'))): ?>
                                        <div class="no-search d-flex align-items-center justify-content-center flex-column gap-2"
                                            style="height: 250px;">
                                            <i class="bx bx-search text-dark" style="font-size: 100px;"></i>
                                            <p style="text-muted">No Notifications matched search term
                                                '<?= get('search') ?>'</p>
                                            <a href="index" class="btn btn-sm btn-primary">View All</a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!is_null(get("author"))): ?>
                                        Filtering By
                                        <b>'<?= $authUserController->userModel->getRecordsBy("id", get("author"))["username"] ?>'</b>
                                        <a href="index" class="btn btn-primary btn-sm rounded-circle"><i
                                                class="fas fa-times"></i></a>
                                        <?php foreach ($notificationsController->getAll() as $notification): ?>
                                            <?php
                                            $userAvatar = !empty($notification["user"]["avatar"]) ? "/icuc_ars/images/users/" . $notification["user"]["avatar"] : "{$appDirPath}assets/images/user-avatar.png";
                                            ?>

                                            <div class="notification d-flex gap-2 p-2 justify-content-between"
                                                style="border-radius: 5px;">
                                                <div class="image-and-content d-flex gap-2">
                                                    <div class="image-case mt-3 mx-2">
                                                        <img src="<?= $userAvatar ?>" alt="" width="50px" height="50px"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="content my-1">
                                                        <div class="notification-title text-dark my-2"
                                                            style="font-weight: bold;"><?= $notification["title"] ?></div>
                                                        <p class="p-0 m-0 text-muted"><?= $notification["body"] ?>
                                                        <div class="d-flex align-items-center gap-4"
                                                            style="font-size: 14px; color: #444;">
                                                            <?php
                                                            $notificationAuthor = $notification["user"]["uniqid"] == $authUser["uniqid"] ? "You" : $notification["user"]["username"];
                                                            ?>
                                                            <span>
                                                                By: <a
                                                                    href="?author=<?= $notification["user"]["id"] ?>"><?= $notificationAuthor ?></a>
                                                            </span>
                                                            <span class="d-flex align-items-center gap-1">
                                                                <span
                                                                    class="icon <?= $notification["is_read"] ? "text-warning" : "text-danger" ?>"
                                                                    style="font-size: 15px;">
                                                                    <i class="mdi mdi-circle"></i>
                                                                </span>
                                                                <span class="unread">
                                                                    <?= $notification["is_read"] ? "Read" : "Unread" ?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <span class="d-flex align-items-center gap-2">
                                                            <span class="icon text-grey" style="font-size: 16px;">
                                                                <i class="mdi mdi-clock-outline"></i>
                                                            </span>
                                                            <b class="text-muted">
                                                                
                                                                <?= $webController->format_date($notification["date_created"]); ?>
                                                                at
                                                                <?= $webController->_time_format_to_am_pm($notification["time_created"]) ?>
                                                            </b>
                                                        </span>
                                                    </div>
                                                    </p>
                                                </div>
                                                <?php if ($notification["user"]["uniqid"] == $authUser["uniqid"]): ?>
                                                    <div class="buttons-container m-2">
                                                        <a href="edit?id=<?= $notification['id'] ?>"
                                                            class="btn btn btn-sm p-0 m-0 text-primary" style="font-size: 17px;"><i
                                                                class="bx bx-pen"></i></a>
                                                        <a href="delete?id=<?= $notification['id'] ?>"
                                                            class="btn btn btn-sm text-danger p-0 m-0" style="font-size: 17px;"><i
                                                                class="bx bx-trash"></i></a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (is_null(get("author")) && is_null(get('search'))): ?>
                                        <?php foreach ($notificationsController->getAll()['data'] as $notificationRecord): ?>
                                            <?php
                                            $dateDiff = $notificationsController->dateTimeDifference('now', $notificationRecord["date_created"]);

                                            $dateHeader = function () use ($dateDiff, $notificationsController, $notificationRecord) {
                                                if ($dateDiff->d == 1) {
                                                    return "Yesterday " . $notificationsController->format_date($notificationRecord["date_created"]);
                                                }

                                                if ($dateDiff->d == 0) {
                                                    return "Today";
                                                }

                                                return "On " . $notificationsController->format_date($notificationRecord["date_created"]);
                                            }
                                                ?>
                                            <div class="text-muted mt-3 mb-2" style="font-weight: bold;">
                                                <?= $dateHeader() ?>
                                            </div>
                                            <?php foreach ($notificationRecord["notifications"] as $notification): ?>
                                                <?php
                                                $userAvatar = !empty($notification["user"]["avatar"]) ? "/icuc_ars/images/users/" . $notification["user"]["avatar"] : "{$appDirPath}assets/images/user-avatar.png";
                                                ?>

                                                <div class="notification d-flex gap-2 p-2 justify-content-between"
                                                    style="border-radius: 5px;">
                                                    <div class="image-and-content d-flex gap-2">
                                                        <div class="image-case mt-3 mx-2">
                                                            <img src="<?= $userAvatar ?>" alt="" width="50px" height="50px"
                                                                class="rounded-circle">
                                                        </div>
                                                        <div class="content my-1">
                                                            <div class="notification-title text-dark my-2"
                                                                style="font-weight: bold;"><?= $notification["title"] ?>
                                                            </div>
                                                            <p class="p-0 m-0 text-muted"><?= $notification["body"] ?>
                                                            <div class="d-flex align-items-center gap-4"
                                                                style="font-size: 14px; color: #444;">
                                                                <?php
                                                                $notificationAuthor = $notification["user"]["uniqid"] == $authUser["uniqid"] ? "You" : $notification["user"]["username"];
                                                                ?>
                                                                <span>
                                                                    By: <a
                                                                        href="?author=<?= $notification["user"]["id"] ?>"><?= $notificationAuthor ?></a>
                                                                </span>
                                                                <span class="d-flex align-items-center gap-1">
                                                                    <span
                                                                        class="icon <?= $notification["is_read"] ? "text-warning" : "text-danger" ?>"
                                                                        style="font-size: 15px;">
                                                                        <i class="mdi mdi-circle"></i>
                                                                    </span>
                                                                    <span class="unread">
                                                                        <?= $notification["is_read"] ? "Read" : "Unread" ?>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            <span class="d-flex align-items-center gap-2">
                                                                <span class="icon text-grey" style="font-size: 16px;">
                                                                    <i class="mdi mdi-clock-outline"></i>
                                                                </span>
                                                                <b class="text-muted">
                                                                    
                                                                    <?= $webController->format_date($notification["date_created"]); ?>
                                                                    at
                                                                    <?= $webController->_time_format_to_am_pm($notification["time_created"]) ?>
                                                                </b>
                                                            </span>
                                                        </div>
                                                        </p>
                                                    </div>
                                                    <div class="buttons-container m-2">
                                                        <?php if ($notification["user"]["uniqid"] == $authUser["uniqid"] && isAdmin()): ?>
                                                            <a href="edit?id=<?= $notification['id'] ?>"
                                                                class="btn btn btn-sm p-0 m-0 text-primary" style="font-size: 17px;"><i
                                                                    class="bx bx-pen"></i></a>
                                                            <a href="delete?id=<?= $notification['id'] ?>"
                                                                class="btn btn btn-sm text-danger p-0 m-0" style="font-size: 17px;"><i
                                                                    class="bx bx-trash"></i></a>
                                                        <?php endif; ?>
                                                        <a href="single?id=<?= $notification['id'] ?>" class="btn text-success btn-sm p-0 m-0" style="font-size: 17px;"><i
                                                                class="far fa-eye"></i></a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        <?php paginationNavigation($notificationsController->getAll()) ?>
                                    <?php endif ?>
                                </div>

                                <?php if (!count($notificationsController->getAll())): ?>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
                                        <span style="font-weight: bold;">No Notifications created yet...</span>
                                    </div>
                                <?php endif; ?>

                                <?php if (get('search') != null): ?>
                                    <?php
                                    $searchedNotifications = $notificationsController->search(get('search'));
                                    ?>
                                    <?php foreach ($searchedNotifications["data"] as $notification): ?>
                                        <?php
                                        $userAvatar = !empty($notification["user"]["avatar"]) ? "/icuc_ars/images/users/" . $notification["user"]["avatar"] : "{$appDirPath}assets/images/user-avatar.png";
                                        ?>

                                        <div class="notification d-flex gap-2 p-2 justify-content-between"
                                            style="border-radius: 5px;">
                                            <div class="image-and-content d-flex gap-2">
                                                <div class="image-case mt-3 mx-2">
                                                    <img src="<?= $userAvatar ?>" alt="" width="50px" height="50px"
                                                        class="rounded-circle">
                                                </div>
                                                <div class="content my-1">
                                                    <div class="notification-title text-dark my-2" style="font-weight: bold;">
                                                        <?= $notification["title"] ?>
                                                    </div>
                                                    <p class="p-0 m-0 text-muted"><?= $notification["body"] ?>
                                                    <div class="d-flex align-items-center gap-4"
                                                        style="font-size: 14px; color: #444;">
                                                        <?php
                                                        $notificationAuthor = $notification["user"]["uniqid"] == $authUser["uniqid"] ? "You" : $notification["user"]["username"];
                                                        ?>
                                                        <span>
                                                            By: <a
                                                                href="?author=<?= $notification["user"]["id"] ?>"><?= $notificationAuthor ?></a>
                                                        </span>
                                                        <span class="d-flex align-items-center gap-1">
                                                            <span
                                                                class="icon <?= $notification["is_read"] ? "text-warning" : "text-danger" ?>"
                                                                style="font-size: 15px;">
                                                                <i class="mdi mdi-circle"></i>
                                                            </span>
                                                            <span class="unread">
                                                                <?= $notification["is_read"] ? "Read" : "Unread" ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <span class="d-flex align-items-center gap-2">
                                                        <span class="icon text-grey" style="font-size: 16px;">
                                                            <i class="mdi mdi-clock-outline"></i>
                                                        </span>
                                                        <b class="text-muted">
                                                            
                                                            <?= $webController->format_date($notification["date_created"]); ?>
                                                            at
                                                            <?= $webController->_time_format_to_am_pm($notification["time_created"]) ?>
                                                        </b>
                                                    </span>
                                                </div>
                                                </p>
                                            </div>
                                            <?php if ($notification["user"]["uniqid"] == $authUser["uniqid"]): ?>
                                                <div class="buttons-container m-2">
                                                    <a href="edit?id=<?= $notification['id'] ?>"
                                                        class="btn btn btn-sm p-0 m-0 text-primary" style="font-size: 17px;"><i
                                                            class="bx bx-pen"></i></a>
                                                    <a href="delete?id=<?= $notification['id'] ?>"
                                                        class="btn btn btn-sm text-danger p-0 m-0" style="font-size: 17px;"><i
                                                            class="bx bx-trash"></i></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if (!count($searchedNotifications["data"])): ?>
                                        <div class="no-search d-flex align-items-center justify-content-center flex-column gap-2"
                                            style="height: 250px;">
                                            <i class="bx bx-search text-dark" style="font-size: 100px;"></i>
                                            <p style="text-muted">No notifications matched search term
                                                '<b><?= get('search') ?></b>'</p>
                                            <a href="index" class="btn btn-sm btn-primary">View All</a>
                                        </div>
                                    <?php else: ?>
                                        <?php paginationNavigation($searchedNotifications) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
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