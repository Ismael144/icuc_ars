<?php

$pageTitle = "Delete User";
include dirname(__DIR__)."/includes/header.php";

use App\controllers\UserController;
use App\enums\UserRole;

authProtect("../auth/signin");

$userController = new UserController;

if (!$userController->userModel->allowByRoles(UserRole::SYSTEM_ADMINISTRATOR, UserRole::STAFF_MEMBER)) $webController->redirect("/icuc_ars/app/dashboard");


if (get('id') == null) {
    header('location: index');
}

$delId = get('id');

$user = $userController->userModel->getRecordsBy("id", $delId);

if (!$user) {
    $webController->sessionHelper->set(_usersmodel__error: "Table Error: You accessed a users table record that does not exist!");
    $webController->redirect("index");
}


if (requestMethod('post')) {
    if ($userController->deleteUser($delId, $user['avatar'])) {
        session_redirect("index", ["_userdepartment__success" => "Delete Successful: You successfully deleted <b>'{$user['username']}'</b> from users table"]);
    }
}

$imgAvatar = empty($user['avatar']) ? "/icuc_ars/app/assets/images/male-img.jpg" : "/icuc_ars/images/users/{$user['avatar']}";

?>

<div id="layout-wrapper">
    <?php
    include dirname(__DIR__) . "/includes/layouts/sidebar.php";
    include dirname(__DIR__) . "/includes/layouts/topbar.php";
    ?>
</div>

<div class="main-content">
    <div class="page-content d-flex align-items-center">
        <div class="col-xl-12 d-flex align-items-center justify-content-center">

            <div class="card" style="width: 21rem;">
                <div class="card-body">
                    <h5 class="card-title my-2">Deleting From <?= ucwords($userController->userModel->tableName); ?> </h5>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <span>
                            <img src="<?= $imgAvatar ?>" alt="<?= $user['username'] ?>" width="35px" height="35px" style="border-radius: 50%;" loading="lazy">
                        </span>
                        <h6>
                            <?= $user['username'] ?>
                        </h6>
                    </div>
                    <p class="card-text my-2">
                        Are you sure you want to delete <b>'<?= $userController->handleNull('username', $user) ?>'</b> from '<?= $userController->userModel->tableName ?>' table
                    </p>
                    <div class="delete-card-footer d-flex align-items-center justify-content-between">
                        <form action="" method="POST">
                            <button class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                <i class="ph-trash"></i>
                                <span>Delete</span></button>
                        </form>
                        <a href="index" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                            <i class="ph-arrow-left"></i>
                            <span>Go back</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once dirname(__DIR__)."/includes/footer.php"; ?>