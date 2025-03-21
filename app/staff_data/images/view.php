<?php 

/** Including The Header */
$pageTitle = "View Records";
include dirname(__DIR__)."/../includes/header.php";

authProtect("../../auth/signin");

use App\controllers\UserController;
use App\controllers\StaffDataController;

$userController = new UserController;
$staffDataController = new StaffDataController;

if (!$staffDataController->handleNull('id', $_GET)) {
    $webController->redirect('index');
}

if (!isset($_GET['page'])) {
    $_GET['page'] = 1;
}

$id = $_GET['id'];

$staffDataImageRecord = $staffDataController->staffDataImagesModel->getRecordsBy("id", $_GET['id']);
$staff = $staffDataController->staffDataModel->getRecordsBy("id", $staffDataImageRecord['data_id']);

$imagePath = "/icuc_ars/images/staff_images/";


?>

<!-- Begin page -->
<div id="layout-wrapper">
    <!-- ========== App Menu ========== -->
    <?php include dirname(__DIR__) . "/../includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    <?php include dirname(__DIR__) . "/../includes/layouts/topbar.php" ?>
    <div class="main-content">
        <div class="page-content">
            <div class="container">
                <a href="<?= $_SERVER['HTTP_REFERER']; ?>" class="d-flex align-items-center gap-2"><i class="ph ph-arrow-left"></i><span>Go back</span></a>
                <div class="row my-4">
                    <div class="col-sm-4">
                        <div class="card mx-3">
                            <div class="card-image">
                                <img src="<?= $imagePath . $staffDataImageRecord['name'] ?>" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="col mt-2">
                        <h4>Properties</h4>
                        <div class="info-item my-2">
                            <b>Belongs To</b>
                            <div><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></div>
                        </div>
                        <div class="info-item my-2">
                            <b>Date Uploaded</b>
                            <div><?= $staffDataController->format_date($staff['date_created']) ?></div>
                        </div>
                        <div class="info-item my-2">
                            <b>Size</b>
                            <div><?= $webController::formatBytes(filesize(dirname(dirname(__DIR__)) . "/../images/staff_images/" . $staffDataImageRecord['name'])) ?></div>
                        </div>
                        <div class="info-item my-2">
                            <b>Type</b>
                            <?php
                                $extension = explode(".", $staffDataImageRecord['name']);
                            ?>
                            <div>Image/<?= ucfirst(end($extension)) ?></div>
                        </div>

                        <a href="<?= $imagePath . $staffDataImageRecord['name'] ?>" target="_blank">View Full Size</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__)."/../includes/footer.php"; ?>
