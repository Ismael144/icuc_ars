<?php
$pageTitle = "Users Index Page";
include_once dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

?>


<?php

use App\controllers\{WebController, UserController};

$webController = new WebController;
$userController = new UserController;

adminAuthProtect("../dashboard");

$notAllowedTableRows = ["password", "avatar", "uniqid", "last_login"];

echo "<div id='layout-wrapper'>";
include dirname(__DIR__) . "/includes/layouts/topbar.php";
include dirname(__DIR__) . "/includes/layouts/sidebar.php";

$currentUser = $userController->getUserInfoById(get('id'));

?>

<div class="main-content">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">User Information</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                            <li class="breadcrumb-item"><a href="index">Users</a></li>
                            <li class="breadcrumb-item active">Single</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <div class="user-card" style="box-shadow: 0;">
            <div class="card p-3">
                <div class="card-image">
                    <img src="<?= userImage($currentUser['avatar']) ?>" alt="" width="120px" height="120px" style="border-radius: 50%;">
                </div>
                <div class="content row">
                    <div class="col">
                        <div class="field my-3">
                            <b class="small my-1">Full Name</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['username'] ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Email Address</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['email'] ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Phone Number</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['phone_number'] ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Gender</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['role'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="field my-3">
                            <b class="small my-1">Last Login</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $userController->_format_date_and_time($currentUser['last_login']) ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Department</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['department'] ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Role</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $currentUser['role'] ?>
                            </div>
                        </div>
                        <div class="field my-3">
                            <b class="small my-1">Date Created</b>
                            <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                                <?= $userController->format_date($currentUser['date_created']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isAdmin() || $authUser['id'] == $currentUser['id']) : ?>
                    <div class="d-flex align-items-center gap-2">
                  <div>
                    <a href="edit?id=<?= $authUser['id'] ?>" class="text-success d-flex align-items-center gap-2" style="display: inline-block;">
                      <span><i class="fas fa-arrow-left"></i></span>
                      <span>
                        Edit
                      </span>
                    </a>
                  </div>
                  <div>
                    <a href=".../attendance/analysis?user=<?= $authUser['id'] ?>" class="text-success d-flex align-items-center gap-2" style="display: inline-block;">
                      <span>
                        Analyse
                      </span>
                    </a>
                  </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
    include_once dirname(__DIR__) . "/includes/footer.php";
    ?>