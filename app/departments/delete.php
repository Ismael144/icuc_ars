<?php 
$pageTitle = "Delete Record";
include dirname(__DIR__)."/includes/header.php";

authProtect("../auth/signin");

use App\controllers\UserDepartmentController;

$userDepartmentController = new UserDepartmentController;

if (get('id') == null) {
    $webController->redirect("index");
}

adminAuthProtect("index");

$delId = get('id');

$deptRecord = $userDepartmentController->userDeptModel->getRecordsBy("id", $delId);

if (requestMethod('POST')) {
    // Before deleting, check whether there a users linked to this department, then unlink them
    $delDeptName = $deptRecord["name"];
    if($userDepartmentController->deleteRecord($delId)) {
        $_SESSION['_userdepartment__success'] = "Delete Successful: You deleted record <b>'{$delDeptName}'</b> successfully"; 
        $webController->redirect("index");
    }
}

?>


<div id="layout-wrapper">
    <?php 
        include dirname(__DIR__)."/includes/layouts/sidebar.php";
        include dirname(__DIR__)."/includes/layouts/topbar.php";
    ?>
</div>

<div class="main-content">
    <div class="page-content d-flex align-items-center">
        <div class="col-xl-12 d-flex align-items-center justify-content-center">

            <div class="card" style="width: 22rem;">
                <div class="card-body">
                    <h5 class="card-title my-2">Deleting From Departments Table </h5>
                    <div class="card-seperator my-2" style="width: 100%; height: 1px; background: #44444424;"></div>
                    <p class="card-text">
                        Are you sure you want to delete <b>'<?= $userDepartmentController->handleNull('name', $deptRecord) ?>'</b> Department from the <b>'departments'</b> available
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