<?php

use App\controllers\UserDepartmentController;

$pageTitle = "User Departments";
include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

$userDepartmentController = new UserDepartmentController;

adminAuthProtect("index");

if (requestMethod('post')) {
    // Do some validations
    $userDepartmentController->formValidation();

    if ($userDepartmentController->noErrors()) {
        if ($userDepartmentController->createDepartment()) {
            session_redirect("index", ["_user_departments__create" => "You successfully created a new department called '{$userDepartmentController->departmentForm['name']}'"]);
        }
    }
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
                            <div class="col-xxl-4 mb-3">
                                <h5 class="card-title mb-3">Departments</h5>
                                <p class="text-muted">View all user departments created here.</p>
                            </div>
                            <div>
                                <form action="" method="post">
                                    <div class="form-element my-2">
                                        <label for="" class="form-label">Name of Department</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter the name of the department you want to create" id="">
                                        <span class="text-danger">
                                            <?= $userDepartmentController->getFieldErr("name") ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-3">
                                        <label for="" class="form-label">Its Description</label>
                                        <textarea type="text" name="description" rows="6" class="form-control" placeholder="Enter a description for the department, (its optional)" id=""></textarea>
                                    </div>
                                    <div class="form-element my-3">
                                        <label for="" class="form-label">Pick Users</label>
                                        <select class="form-select" id="unassigned-users" name="unassigned_users" data-placeholder="Select Users" multiple data-multi-select>
                                            <?php foreach ($userDepartmentController->getUnassignedUsers() as $unassignedUser):  ?>
                                                    <option value="<?= $unassignedUser['id'] ?>" <?= in_array($unassignedUser, $userDepartmentController->departmentForm) ? "selected" : "" ?>><?= $unassignedUser['username'] ?></option>
                                                <?php endforeach;  ?>
                                        </select>
                                        <span class="text-danger">
                                            <?= $userDepartmentController->getFieldErr('unassigned_users') ?>
                                        </span>
                                    </div>
                                    <input type="submit" class="btn btn-success" value="Create Department">
                                </form>
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