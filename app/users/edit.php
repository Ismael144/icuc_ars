<?php
$pageTitle = "Edit User";
# Including the header
include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

use App\{enums\UserRole, controllers\UserController, controllers\UserDepartmentController};


$userController = new UserController;

# Path to Images
$path = dirname(__DIR__) . "/../images/users/";


# Redirect when user is not authenticated
authProtect("{$appDirPath}auth/signin");

if (get('id') == null) {
    $webController->redirect('index');
}

$departments = (new UserDepartmentController)->userDeptModel->fetchAllData();

$id = get('id');

if ($webController->handleNull("id", $authUser) == $id) {
    $webController->redirect("index");
}

$user = $userController->userModel->getRecordsBy("id", (int)$id);

if (!$user) {
    $webController->sessionHelper->set(_usersmodel__error: "Table Error: You accessed a users table record that does not exist!");
    $webController->redirect("index");
}

if ($user['role_id'] == UserRole::SYSTEM_ADMINISTRATOR && $authUser['role_id'] == UserRole::STAFF_MEMBER || $user['role_id'] == UserRole::STAFF_MEMBER && $authUser['role_id'] == UserRole::STAFF_MEMBER) {
    $webController->sessionHelper->set(_user_session__notice: "You have no privilege to carry out this operation.");
    $webController->redirect("index");
}

$roles = [
    "staff member"  => $authUser['role_id'] == UserRole::SYSTEM_ADMINISTRATOR->value ? UserRole::STAFF_MEMBER->value : "",
];

# Form fields 
$username = $userController->handleNull("username", $userController->user);
$email = $userController->handleNull("email", $userController->user);
$password = $userController->handleNull("password", $userController->user);
$role = empty($userController->handleNull("role", $userController->user)) ? $user['role_id'] : $userController->handleNull("role", $userController->user);
$department = empty($userController->handleNull("department", $userController->user)) ? $user['dept_id'] : $userController->handleNull("department", $userController->user);
$phoneNumber = $userController->handleNull("phone_number", $userController->user) ?: $user['phone_number'];
$gender = $userController->handleNull('gender', $userController->user) ?: $user['gender']; 

$errorsArray = [];

if (requestMethod('post')) {
    $userAvatar = $_FILES['userAvatar'];
    $avatarName = "";

    if (strlen($userAvatar['name'])) $avatarName = $webController->imageHelper->uploadImage($userAvatar, $path);

    // Validations Section 
    # ...................................
    if (empty($username)) {
        $errorsArray['username'] = "This field is required...";
    }

    // Validate Email 
    if (!empty($email)) {
        if (!$userController->validateHelper->validateEmail($email)) $errorsArray['email'] = "You entered an invalid email";
    } else $errorsArray['email'] = "This field is required..";

    if (empty($role)) $errorsArray['role'] = "Please select a role to assign to user <br>Role: $role";

    if (!empty($phoneNumber)) {
        if (!$userController->validateHelper->validatePhoneNumber($userController->handleNull("phone_number", $userController->user))) $errorsArray['phoneNumber'] = "You entered an invalid phone number";
    } else $errorsArray['phoneNumber'] = "This field is required..";

    if (empty($gender)) {
        $errorsArray['gender'] = "This field is required...";
    }

    # Validate password
    if (!empty($password)) {
        # Will Prevent it from rehashing the same password.
        if (!$userController->validateHelper->checkPasswordStrength($password)) {
            $errorsArray['password'] = "Password Not Strong Enough, make sure it contains numbers, lowercase or uppercase, and special characters.";
        }
    } else $userController->user['password'] = $user['password'];

    if (!count($errorsArray)) {
        if ($userController->updateUser($avatarName, $user)) {
            $webController->sessionHelper->set(_usersession__update: "You successfully updated the record of <b>'$username'</b> in {$userController->getTableName()} table.");
            $webController->redirect("index");
        }
    } else {
        $webController->sessionHelper->set(_usersession__info: 'User Editing: Please sort all the errors inorder to proceed ...');
    }
}

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
            <a href="index" class="d-flex align-items-center gap-2"><i class="ph ph-arrow-left"></i><span>Go back</span></a>
            <div class="page-title-box mt-3 d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit User</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="index">Users</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-4">
                                <h5 class="card-title mb-3">Edit User</h5>
                                <p class="text-muted mb-0">Edit this user.</p>
                            </div>
                            <div class="col-xxl-8">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Username</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Username..." value="<?= strlen($username) ? $userController->user['username'] : $user['username'] ?>" name="username">
                                        <span class="text-danger">
                                            <?= $userController->handleNull("username", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Email Address</label>
                                        <input type="email" class="form-control form-control-md" placeholder="Your Email Address..." value="<?= strlen($email) ? $userController->user['email'] : $user['email'] ?>" name="email">
                                        <span class="text-danger">
                                            <?= $userController->handleNull("email", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Role</label>
                                        <select class="form-select" placeholder="Your Phone Number..." name="role">
                                            <option value="">Choose Role For New User</option>
                                            <?php foreach ($roles as $key => $value) : ?>
                                                <?php if (!empty($value)) : ?>
                                                    <option value="<?= $value ?>" <?= $role == $value ? "selected" : "" ?>><?= ucwords($key) ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger">
                                            <?php $userController->handleNull("role", $errorsArray); ?>
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
                                        </div>
                                        <span class="text-danger"><?= handleNull('gender', $errorsArray) ?></span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Select Department</label>
                                        <select class="form-select" placeholder="Your Phone Number..." name="department">
                                            <option value="">Select department to assign the user to</option>
                                            <?php foreach ($departments as $deptRecord) : ?>
                                                <option value="<?= $deptRecord["id"] ?>" <?= $deptRecord["id"] == $department ? "selected" : "" ?>><?= ucwords($deptRecord["name"]) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger">
                                            <?php $userController->handleNull("role", $errorsArray); ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Password</label>
                                        <input type="password" class="form-control form-control-md" placeholder="Your Password..." value="<?= $userController->handleNull("password", $userController->user) ?>" name="password">
                                        <div class="alert border-0 my-2 p-2 alert-warning mb-2" role="alert">
                                            Leave the password blank to use the recent password
                                        </div>
                                        <div class="text-danger">
                                            <?= $userController->handleNull("password", $errorsArray) ?>
                                        </div>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Phone Number</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Phone Number..." value="<?= strlen($phoneNumber) ? $phoneNumber : $user['phone_number'] ?>" name="phone_number">
                                        <span class="text-danger">
                                            <?= $userController->handleNull("phoneNumber", $errorsArray) ?>
                                        </span>
                                    </div>
                                    <div class="form-element">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Upload User Avatar</label>
                                        <span class="text-danger">
                                            <?= $webController->imageHelper->errors ?>
                                        </span>
                                        <input type="file" name="userAvatar" id="" class="form-control mb-1">
                                        <div class="text-info mt-2">
                                            <?= strlen($user['avatar']) ? "Current Image: <a href='../../images/users/{$user['avatar']}'>{$user['avatar']}</a>" : "" ?>
                                        </div>
                                    </div>
                                    <div class="form-element my-3">
                                        <input type="submit" value="Edit" class="btn btn-md btn-success">
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