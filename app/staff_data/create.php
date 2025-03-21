<?php
$pageTitle = "Create Record";
include_once dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

use App\controllers\UserController;
use App\controllers\StaffDataController;

$staffDataController = new StaffDataController;
$userController = new UserController;

$firstName = $staffDataController->handleNull("first_name", $staffDataController->staffDataForm);
$lastName = $staffDataController->handleNull("last_name", $staffDataController->staffDataForm);
$staffUserId = $staffDataController->handleNull("staff", $staffDataController->staffDataForm);

$staffUsers = $userController->userModel->getUsersTableData();

if (requestMethod('post')) {
    $staffDataController->formValidation();

    if ($staffDataController->noErrors()) {
        if ($staffDataController->createRecord()) {
            $fullName = $firstName . ' ' . $lastName;
            $webController->sessionHelper->set(_staffdata__create: "You Successfully Created <b>'$fullName'</b>");
            $webController->redirect("index");
        }
    } else {
        $webController->sessionHelper->set(_usersession__info: 'Staff Record Creation: Please sort all the errors inorder to proceed ...');
    }
}

?>

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ========== App Menu ========== -->
    <?php include "../includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <?php include "../includes/layouts/topbar.php" ?>
    <!-- Vertical Overlay-->
    <div class="main-content">
        <div class="page-content">
            <a href="index" class="d-flex align-items-center gap-2"><i class="ph ph-arrow-left"></i><span>Go back</span></a>
            <div class="page-title-box mt-3 d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Record</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="index">Staff</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-4">
                                <h5 class="card-title mb-3">Create Record</h5>
                                <p class="text-muted mb-0">Create a new Record from here, please make sure to fill in all the required fields inorder to continue.</p>
                            </div>
                            <div class="col-xxl-8">
                                <form action="" method="post" enctype="multipart/form-data" id="dataEditForm">
                                    <span class="text-danger">
                                        <?= $staffDataController->handleNull("fullName", $staffDataController->fieldErrors) ?>
                                    </span>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2 form-label" style="color: black; font-size: 14px;">First Name</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your First Name..." name="first_name" value="<?= $firstName ?>">
                                        <span class="text-danger">
                                            <?= $staffDataController->handleNull("first_name", $staffDataController->fieldErrors) ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2 form-label" style="color: black; font-size: 14px;">Last Name</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Last Name..." value="<?= $lastName ?>" name="last_name">
                                        <span class="text-danger">
                                            <?= $staffDataController->handleNull("last_name", $staffDataController->fieldErrors) ?>
                                        </span>
                                    </div>
                                    <?php if (isAdmin()) : ?>
                                        <div class="form-element my-2">
                                            <label for="" class="form-label my-2">Select Staff Member</label>
                                            <select name="staff" id="" class="form-select">
                                                <option value="">Select A Staff Member User</option>
                                                <option value="<?= $authUser["id"] ?>" <?= $staffUserId == $authUser["id"] || get('id') == $staffUserId ? "selected" : "" ?>><?= $authUser["username"] ?> (You)</option>
                                                <?php foreach ($userController->userModel->getUsersTableData() as $user) : ?>
                                                    <option value="<?= $user["id"] ?>" <?= $staffUserId == $user["id"] ? "selected" : "" ?>><?= $user["username"] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger">
                                                <?= $staffDataController->handleNull("staff", $staffDataController->fieldErrors) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-element my-3">
                                        <input type="submit" value="Create Profile" class="btn btn-md btn-success">
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__) . "/includes/footer.php"; ?>