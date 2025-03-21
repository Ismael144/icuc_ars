<?php
$pageTitle = "Change User Information";
include dirname(__DIR__) . "/../includes/header.php";

authProtect("../../auth/signin");

$userImage = $authUserController->authUser['avatar'] !== null ? "/icuc_ars/images/" . $authUserController->authUser['avatar'] : "/icuc_ars/app/assets/images/male-img.jpg";

$fieldErrors = [];
if (requestMethod('POST')) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    $fieldErrors = &$authUserController->fieldErrors;

    // Some validations
    if (empty($oldPassword)) {
        $fieldErrors["old_password"] = "This field is required..."; 
    }

    if (empty($newPassword)) {
        $fieldErrors["new_password"] = "This field is required..."; 
    } else {
        if (!$authUserController::$validateHelper->checkPasswordStrength($newPassword)) $fieldErrors["new_password"] = "Password Not Strong Enough, make sure it contains numbers, lowercase or uppercase, and special characters";
    }


    if (!count($authUserController->fieldErrors)) {
        if ($authUserController->updatePassword($oldPassword, $newPassword)) {
            $authUserController::$sessionHelper->set("authuser__update");
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
                        <h4 class="mb-sm-0">Change Password</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">User Profile</a></li>
                                <li class="breadcrumb-item active">Change Password</li>
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
                                    <h5 class="card-title mb-3">Change Password</h5>
                                    <p class="text-muted">Change or edit your user Password from here.</p>
                                </div>
                                <div class="col-xxl-8">

                                    <form action="" method="post" enctype="multipart/form-data">
                                        <span class="text-danger">
                                            <?= handleNull("error", $fieldErrors) ?>
                                        </span>
                                        <div class="mb-3">
                                            <label for="" class="form-label">Old Password</label>
                                            <input type="password" class="form-control" name="oldPassword" value="<?= handleNull("oldPassword", $_POST) ?>" placeholder="Enter Old Password here..." />
                                            <span class="text-danger">
                                                <?= $authUserController->handleNull("old_password", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="newPassword" value="<?= handleNull("newPassword", $_POST) ?>" placeholder="Enter The New Password here..." />
                                            <span class="text-danger">
                                                <?= $authUserController->handleNull("new_password", $fieldErrors) ?>
                                            </span>
                                        </div>
                                        <input type="submit" value="Edit Password" class="btn btn-success">
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