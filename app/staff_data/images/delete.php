<?php
$pageTitle = "Delete Image";
include dirname(__DIR__)."/../includes/header.php";

authProtect("../../auth/signin");

use App\controllers\StaffDataController;

$staffDataController = new StaffDataController;

if (get('id') == null) {
    $staffDataController->redirect('../index');
}

$delId = get('id');

$imagePath = "/icuc_ars/images/staff_images/";
$staffImageRecord = $staffDataController->staffDataImagesModel->getRecordsBy("id", $delId);
$staffMember = $staffDataController->staffDataModel->getRecordsBy('id', $staffImageRecord['data_id']);

if (!$staffImageRecord) {
    session_redirect("../index", ["_staff_data__error" => "Data Access Error: You tried to accessed a record that does not exist"]);
}

if (requestMethod('POST')) {
    if ($staffDataController->staffDataImagesModel->deleteItemFromTable("id", $delId)) {
        if ($staffDataController->imageHelper->removeImage(dirname(__DIR__)."/../../images/staff_images/", $staffImageRecord['name'])) {
            session_redirect("index?id={$staffMember['user_id']}", ["_staff_images__success" => "Delete Successful: You successfully deleted this image"]);
        } else {
            echo "not working";
        }
    }
}

?>

<div id="layout-wrapper">
    <?php
    include dirname(dirname(__DIR__)) . "/includes/layouts/sidebar.php";
    include dirname(dirname(__DIR__)) . "/includes/layouts/topbar.php";
    ?>
</div>

<div class="main-content">
    <div class="page-content d-flex align-items-center">
        <div class="col-xl-12 d-flex align-items-center justify-content-center">

            <div class="card" style="width: 18rem; overflow: hidden;">
                <div class="card-image">
                <img src="<?= $imagePath . $staffImageRecord['name'] ?>" alt="" style="height: 200px; object-fit: cover; object-position: center; width: 100%;" class="img-fluid">
                </div>
                <div class="card-body">
                    <h5 class="card-title my-2">Deleting From <?= ucwords($staffDataController->staffDataModel->tableName); ?> </h5>
                    <p class="card-text">
                        Are you sure you want to delete this image from the <b>'<?= $staffDataController->staffDataModel->tableName ?>'</b> table
                    </p>
                    <div class="delete-card-footer d-flex align-items-center justify-content-between">
                        <form action="" method="POST">
                            <button class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                <i class="ph-trash"></i>
                                <span>Delete</span></button>
                        </form>
                        <a href="index?id=<?= $staffImageRecord["data_id"] ?>" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                            <i class="ph-arrow-left"></i>
                            <span>Go back</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once dirname(__DIR__)."/../includes/footer.php"; ?>