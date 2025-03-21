<?php

/** Including The Header */
$pageTitle = "Create User";
include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

use App\{enums\UserRole, controllers\UserController, controllers\UserDepartmentController};

$path = dirname(__DIR__) . "/../images/users/";
$userController = new UserController;

if (!$userController->userModel->allowByRoles(UserRole::SYSTEM_ADMINISTRATOR, UserRole::STAFF_MEMBER))
    $webController->redirect("/icuc_ars/app/dashboard");

# Form fields 
$username = $userController->handleNull("username", $userController->user);
$email = $userController->handleNull("email", $userController->user);
$password = $userController->handleNull("password", $userController->user);
$role = $userController->handleNull("role", $userController->user);
$department = $userController->handleNull("department", $userController->user);
$phoneNumber = $userController->handleNull("phone_number", $userController->user);
$gender = $userController->handleNull("gender", $userController->user);

$roles = [
    "staff Member" => $authUser['role_id'] == UserRole::SYSTEM_ADMINISTRATOR->value ? UserRole::STAFF_MEMBER->value : "",
];


$departments = (new UserDepartmentController)->userDeptModel->fetchAllData();
$errorsArray = [];

// global $username, $email, $password, $phoneNumber, $userController;

if (requestMethod('POST')) {
    $userController->formValidation();

    if (empty($role))
        $userController->fieldErrors['role'] = "Please select a role to assign to user";

    if ($userController->noErrors() and !strlen($userController->imageHelper->errors)) {
        if ($userController->createUser()) {
            $webController->sessionHelper->set(_usersession__create: "You created user <b>'$username'</b> successfully");
            $webController->redirect("index");
        }
    } else {
        $webController->sessionHelper->set(_usersession__info: 'User Creation: Please sort all the errors inorder to proceed ...');
    }
}

$errorsArray = $userController->fieldErrors;
$imageError = $userController->imageHelper->errors;

$parsedAllowedImgFormats = implode(', ', array_map(function ($item) {
    return ".$item";
}, $userController->imageHelper->getValidExtensions()));

?>

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ========== App Menu ========== -->
    <?php include "../includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    <?php include "../includes/layouts/topbar.php" ?>
    <div class="main-content">
        <div class="page-content">
            <a href="index" class="d-flex align-items-center gap-2"><i class="ph ph-arrow-left"></i><span>Go
                    back</span></a>
            <div class="page-title-box mt-3 d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create User</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="index">Users</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-4">
                                <h5 class="card-title mb-3">Create User</h5>
                                <p class="text-muted mb-0">Create a new user from here, please make sure to fill in all
                                    the required fields inorder to continue.</p>
                            </div>
                            <div class="col-xxl-8">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Full Name</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Full Name..." value="<?= $username ?>" name="username">
                                        <span class="text-danger">
                                            <?= $userController->handleNull("username", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="row">

                                        <div class="col form-element my-2">
                                            <label for="" class="my-2" style="color: black; font-size: 14px;">Email
                                                Address</label>
                                            <input type="email" class="form-control form-control-md" placeholder="Your Email Address..." value="<?= $email ?>" name="email">
                                            <span class="text-danger">
                                                <?= $userController->handleNull("email", $errorsArray) ?>
                                            </span>
                                        </div>
                                        <div class="col form-element my-2">
                                            <label for="" class="my-2" style="color: black; font-size: 14px;">Password</label>
                                            <input type="password" class="form-control form-control-md" placeholder="Your Password..." value="<?= $password ?>" name="password">
                                            <div class="text-danger">
                                                <?= $userController->handleNull("password", $errorsArray) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Role</label>
                                        <select class="form-select" placeholder="Your Phone Number..." value="<?= $role ?>" name="role">
                                            <option value="">Choose Role For New User</option>
                                            <?php foreach ($roles as $key => $value) : ?>
                                                <?php if (!empty($value)) : ?>
                                                    <option value="<?= $value ?>" <?php echo $role == $value ? "selected" : "" ?>>
                                                        <?= ucwords($key) ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger">
                                            <?= $userController->handleNull("role", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element">
                                        <label for="" class="form-label">Gender</label>
                                        <div class="check-form-group d-flex align-items-center" style="gap: 20px;">
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="radio" name="gender" id="male" value="0" <?= $gender == "0" ? "checked" : "" ?>>
                                                <label for="male">Male</label>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="radio" name="gender" id="female" value="1" <?= $gender == "1" ? "checked" : "" ?>>
                                                <label for="female">Female</label>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="radio" name="gender" id="custom" value="2" <?= $gender == "2" ? "checked" : "" ?>>
                                                <label for="custom">Custom</label>
                                            </div>
                                            <?= $gender; ?>
                                        </div>
                                        <span class="text-danger"><?= $userController->getFieldErr('gender') ?></span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Select
                                            Department</label>
                                        <select class="form-select" name="department">
                                            <option value="">Select Department to assign to the new user</option>
                                            <?php foreach ($departments as $deptRecord) : ?>
                                                <?php if (!empty($deptRecord)) : ?>
                                                    <option value="<?= $deptRecord["id"] ?>" <?php echo $department == $deptRecord["id"] ? "selected" : "" ?>>
                                                        <?= ucwords($deptRecord["name"]) ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger">
                                            <?= $userController->handleNull("department", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Phone
                                            Number</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Phone Number..." value="<?= $phoneNumber ?>" name="phone_number">
                                        <span class="text-danger">
                                            <?= $userController->handleNull("phoneNumber", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Upload
                                            Avatar</label>
                                        <span class="text-danger">
                                            <?= $webController->imageHelper->errors ?>
                                        </span>
                                        <input type="file" name="userAvatar" id="" class="form-control mb-1" accept="<?= $parsedAllowedImgFormats ?>">
                                        <span class="text-danger">
                                            <?= $imageError ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-3">
                                        <input type="submit" value="Create User" class="btn btn-md btn-success">
                                    </div>
                                </form>

                            </div><!--end col-->
                        </div><!--end row-->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once dirname(__DIR__) . "/includes/footer.php"; ?>