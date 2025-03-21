<?php
$pageTitle = "Change User Information";
include dirname(__DIR__) . "/../includes/header.php";

authProtect("../../auth/signin");

$userImage = $authUserController->authUser['avatar'] !== null ? "/icuc_ars/images/" . $authUserController->authUser['avatar'] : "/icuc_ars/app/assets/images/male-img.jpg";

$fieldErrors = [];
if (requestMethod('POST')) {

    // Form validation here 
    {
        $username = $authUserController->handleNull("username", $authUserController->user);
        $email = $authUserController->handleNull("email", $authUserController->user);
        $role = $authUserController->handleNull("role", $authUserController->user);
        $avatar = $authUserController->handleNull("avatar", $authUserController->user);
        $phoneNumber = $authUserController->handleNull("phone_number", $authUserController->user);

        if (!empty($username)) {
            if (!$authUserController::$validateHelper->isUsernameValid($username)) $authUserController->fieldErrors['username'] = "Your username is invalid, make sure it does not contain characters like '$@#$%^'";
        } else $authUserController->fieldErrors['username'] = "This field is required...";

        // Validate Email 
        if (!empty($email)) {
            if (!$authUserController::$validateHelper->validateEmail($email)) $authUserController->fieldErrors['email'] = "You entered an invalid email";
        } else $authUserController->fieldErrors['email'] = "This field is required..";


        if (!empty($phoneNumber)) {
            if (!$authUserController::$validateHelper->validatePhoneNumber($authUserController->handleNull("phone_number", $authUserController->user))) $authUserController->fieldErrors['phoneNumber'] = "You entered an invalid phone number";
        } else $authUserController->fieldErrors['phoneNumber'] = "This field is required..";
    }

    $fieldErrors = &$authUserController->fieldErrors;

    if (!count($authUserController->fieldErrors)) {
        if ($authUserController->update()) {
            $authUserController::$sessionHelper->set(authuser__update: 'Profile changed successfully...');
            $authUserController->redirect("user");
        } else {
            $fieldErrors["error"] = "Old Password does not match";
        }
    }
}

?>

<div id="layout-wrapper">
    <?php
    include dirname(__DIR__) . "/../includes/layouts/sidebar.php";
    include dirname(__DIR__) . "/../includes/layouts/topbar.php";
    ?>
</div>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Change Profile</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">User Profile</a></li>
                                <li class="breadcrumb-item active">Change Profile</li>
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
                                <div class="col-xxl-4">
                                    <h5 class="card-title mb-3">Change Profile</h5>
                                    <p class="text-muted">Change or edit your user profile from here.</p>
                                </div>
                                <div class="col-xxl-8">

                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= $authUserController->authUser['username'] ?>" placeholder="Like John Doe" />
                                            <span class="text-danger">
                                                <?= $authUserController->handleNull("username", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <div class="row">
                                            <div class="col mb-3">
                                                <label for="" class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="<?= $authUserController->authUser['email'] ?>" aria-describedby="emailHelpId" placeholder="abc@mail.com" />
                                                <span class="text-danger">
                                                    <?= $authUserController->handleNull("email", $fieldErrors) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">Phone Number</label>
                                            <input type="number" class="form-control" name="phone_number" value="<?= $authUserController->authUser['phone_number'] ?>" aria-describedby="emailHelpId" placeholder="Your Phone Number" />
                                            <span class="form-text text-danger">
                                                <?= $authUserController->handleNull("phone_number", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">User Avatar</label>
                                            <input type="file" class="form-control" name="avatar" />
                                            <div class="my-2">
                                                Current Image: <a href="<?= userImage($authUser['avatar']) ?>"><?= $authUser['avatar'] ?: "None" ?></a>
                                            </div>
                                            <span class="form-text text-danger">
                                                <?= $authUserController->handleNull("avatar", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <input type="submit" value="Edit Profile" class="btn btn-success">
                                    </form>

                                </div><!--end col-->
                            </div><!--end row-->
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->

        </div>
    </div>
</div>