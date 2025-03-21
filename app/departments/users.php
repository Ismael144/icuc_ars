<?php

use App\controllers\UserDepartmentController;

$pageTitle = "User Departments";
include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

$userDepartmentController = new UserDepartmentController;

$userDepartmentPaginatedData = $userDepartmentController->getUserDepartmentData();

if (get('id') == null) session_redirect("index", ["_userdepartment__error", "Access Error: An ID is required in order to proceed!"]);

$deptRecord = $userDepartmentController->userDeptModel->getRecordsBy("id", get('id'));

if (!$deptRecord) {
    session_redirect("index", ["_userdepartment__error" => "Access Error: You accessed a record that does not exist, please try again..."]);
}

?>


<div id="layout-wrapper">
    <?php
    include dirname(__DIR__) . "/includes/layouts/sidebar.php";
    include dirname(__DIR__) . "/includes/layouts/topbar.php";
    ?>
</div>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Users Under Department</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Departments</a></li>
                                <li class="breadcrumb-item active">Users</li>
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
                            <a href="index" class="">Go back</a>
                            <div class="intro-header mb-4 mx-1">
                                <h4 class="text-capitalize mt-3"><?= $deptRecord["name"] ?></h4>
                                <p>
                                    <?= $deptRecord["description"] ?: "-- No Description --" ?>
                                </p>
                            </div>
                            <div class="users-card" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                <?php foreach ($userDepartmentController->getUsersByDepartment(get('id'))["users"] as $user) : ?>
                                    <div class="card p-3 d-flex border user-card">
                                        <div class="col text-center">
                                            <img src="<?= userImage($user["avatar"]) ?>" alt="" style="width: 
                                                80px; height: 80px; object-fit: cover; object-position: center; border-radius: 50%;">
                                        </div>
                                        <div class="mt-3 text-center">
                                            <div class="col my-2 text-capitalize" style="font-weight: bold">
                                                <?= $user["username"] ?>
                                            </div>
                                            <!-- <div class="email my-2">
                                                <?= $user["email"] ?>
                                            </div> -->
                                            <div class="my-2 text-center mt-3" style="font-weight: 600;">
                                                <?php if (isAdmin()): ?>
                                                <a href="../users/single?id=<?= $user['id'] ?>" class="d-flex align-items-center justify-content-center gap-2 text-success" style="font-size: 12px !important;">
                                                    <span>
                                                        View User Profile 
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (!$userDepartmentController->getUsersByDepartment(get('id'))['users_count']) : ?>
                                    <div class="no-record-container d-flex align-items-center flex-column gap-2 justify-content-center" style="height: 300px;">
                                        <b>
                                            No Users Are Under This Department
                                        </b>
                                        <?php if (!isAdmin()) : ?>
                                            <a href="../users/index" class="btn btn-success">
                                                Add Users
                                            </a>
                                        <?php endif ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once dirname(__DIR__) . "/includes/footer.php"; ?>