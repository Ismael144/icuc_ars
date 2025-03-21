<?php
$pageTitle = "Edit Record";
include dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

use App\controllers\UserController;
use App\controllers\StaffDataController;

$staffDataController = new StaffDataController;

if (get('id') == null) {
    $webController->redirect('index');
}

$recordId = $staffDataController->validateHelper->filter(get('id'));
$deptRecord = $staffDataController->staffDataModel->getRecordsBy("id", get('id'));

if (!$deptRecord) {
    $staffDataController->redirect("index");
}

$userController = new UserController; 

$firstName = $staffDataController->handleNull("first_name", $staffDataController->staffDataForm);
$lastName = $staffDataController->handleNull("last_name", $staffDataController->staffDataForm);
$staffUserId = $staffDataController->handleNull("email", $staffDataController->staffDataForm);

$staffUsers = $userController->userModel->getUsersTableData();

if (requestMethod('POST')) {
    $staffDataController->formValidation();

    if (!count($staffDataController->fieldErrors)) {
        if ($staffDataController->updateDataRecord($deptRecord)) {
            $fullName = $firstName . ' ' . $lastName;
            $webController->sessionHelper->set(_systemData__Update: "You successfully updated <b>'$fullName'</b>.");

            $webController->redirect("index");
        }
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
                                <p class="text-muted mb-0">Edit a new user from here, please make sure to fill in all the required fields inorder to continue.</p>
                            </div>
                            <div class="col-xxl-8">
                                <p>Updating record <b>'<?= $deptRecord['first_name'] . ' ' . $deptRecord['last_name'] ?>'</b></p>
                                <form action="" method="post" enctype="multipart/form-data" id="dataEditForm">
                                    <span class="text-danger"><?= $staffDataController->handleNull("fullName", $staffDataController->fieldErrors) ?></span>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">First Name</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your First Name..." name="first_name" value="<?= strlen($firstName) ? $firstName : $deptRecord['first_name'] ?>">
                                        <span class="text-danger">
                                            <?= $staffDataController->handleNull("first_name", $staffDataController->fieldErrors) ?>
                                        </span>
                                    </div>
                                    <div class="form-element my-2">
                                        <label for="" class="my-2" style="color: black; font-size: 14px;">Last Name</label>
                                        <input type="text" class="form-control form-control-md" placeholder="Your Last Name..." value="<?= strlen($lastName) ? $lastName : $deptRecord['last_name'] ?>" name="last_name">
                                        <span class="text-danger">
                                            <?= $staffDataController->handleNull("last_name", $staffDataController->fieldErrors) ?>
                                        </span>
                                    </div>
                                    <?php if (isAdmin()) : ?>
                                        <div class="form-element my-2">
                                            <label for="" class="form-label my-2">Select Staff Member</label>
                                            <select name="staff" id="" class="form-select">
                                                <option value="">Select A Staff Member</option>
                                                <?php
                                                function selected($id) {
                                                    global $staffDataController, $deptRecord; 
                                                    
                                                    return isset($_POST['staff']) ? (handleNull("staff", $staffDataController->staffDataForm) == $id ? "selected" : "") : ($id == $deptRecord["user_id"] ? "selected" : "");
                                                }
                                                ?>
                                                <option value="<?= $authUser["id"] ?>" <?= selected($authUser["id"]) ?>><?= $authUser["username"] ?> (You)</option>
                                                <?php foreach ($userController->userModel->getUsersTableData() as $user) : ?>
                                                    <option value="<?= $user["id"] ?>" <?= selected($user["id"]) ?>><?= $user["username"] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger">
                                                <?= $staffDataController->handleNull("staff", $staffDataController->fieldErrors) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
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