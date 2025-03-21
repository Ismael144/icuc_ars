<?php

use App\controllers\UserDepartmentController;

$pageTitle = "User Departments";

include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

$userDepartmentController = new UserDepartmentController;

$userDepartmentPaginatedData = $userDepartmentController->getUserDepartmentData();

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
                        <h4 class="mb-sm-0">Departments</h4>

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
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="col-xxl-4 mb-3">
                                    <h5 class="card-title mb-3">Departments</h5>
                                    <p class="text-muted">View all user departments created here.</p>
                                </div>
                                <?php if (isAdmin()): ?>
                                    <div>
                                        <a href="create" class="btn btn-success">Create</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <style>
                                .department-info {
                                    display: grid; 
                                    grid-template-columns: repeat(2, 1fr); 
                                    gap: 15px;
                                }

                                @media screen and (max-width: 904px) {
                                    .department-info {
                                        display: grid;
                                        grid-template-columns: repeat(1, 1fr); 
                                    }

                                    .department-info > .card h3 {
                                        font-size: 15px !important;
                                    }

                                    .dept-collection {
                                        margin: 0 !important;
                                    }

                                    .page-content {
                                        padding-right: 0 !important; 
                                        padding-left: 0 !important; 
                                    }
                                }
                            </style>
                            <div class="row mx-2 department-info">
                                <?php foreach ($userDepartmentPaginatedData["data"] as $department): ?>
                                    <div class="card border dept-collection">
                                        <div class="card-header">
                                            <h3 class="card-title text-capitalize">
                                                <?= $department["name"] ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($department["description"] == null): ?>
                                                <small>
                                                    <b>-- No Description Given --</b>
                                                </small>
                                            <?php else: ?>
                                                <?= $department["description"] ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="users-link mx-3 mb-3">
                                            <a href="users?id=<?= $department["id"] ?>" class="text-success bold"
                                                title="View users that are linked to this department" class="d-flex align-items-center gap-2" style="font-size: 15px !important">
                                                <span>
                                                    <i class="bx bx-user"></i>
                                                </span>
                                                <?= $userDepartmentController->getUsersByDepartment($department["id"])["users_count"] ?: "No" ?>
                                                Users</i></a>
                                        </div>
                                        <?php if (isAdmin()): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="edit-link mx-2 mb-3">
                                                    <a href="edit?id=<?= $department["id"] ?>"
                                                        class="btn btn-primary btn-sm ">Edit</a>
                                                </div>
                                                <div class="edit-link mb-3">
                                                    <a href="delete?id=<?= $department["id"] ?>"
                                                        class="btn btn-danger btn-sm ">Delete</a>
                                                </div>
                                                <div class="analysis-link mb-3 mx-2">
                                                    <a href="../attendance/analysis?dept=<?= $department["id"] ?>"
                                                        class="btn btn-success btn-sm ">Analyse</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-footer">
                                            Created On
                                            <?= $userDepartmentController->format_date($department["date_created"]) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php paginationNavigation($userDepartmentPaginatedData) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once dirname(__DIR__) . "/includes/footer.php"; ?>