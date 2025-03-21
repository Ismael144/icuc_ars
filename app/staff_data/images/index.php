<?php 
$pageTitle = "Image Archive"; 
include_once dirname(__DIR__)."/../includes/header.php";

use App\controllers\StaffDataController;

authProtect("../../auth/signin");

$path = dirname(__DIR__) . "/../images/users/";
$staffDataController = new StaffDataController;

$id = get('id');

if ($id == null && isAdmin()) {
    $webController->redirect('index');
}

if ($id == null && !isAdmin()) {
    $id = $authUser["id"];
}

if (get('page') == null) {
    $_GET['page'] = 1;
}

$staffData = $staffDataController->staffDataModel->getRecordsBy("user_id", $id);

if ($staffData == false && isAdmin() && $authUser['id'] == $id) {
    $id = $authUser["id"];
    $staffData = $staffDataController->staffDataModel->getRecordsBy("user_id", $id);
    if ($staffData == false) session_redirect("../create?id=$id", ["_staff_data__info" => " One Step Close: We need just a couple of information inorder to continue, help us fill in below."]);
}

if ($staffData == false && isAdmin() && $authUser['id'] == $id) {
    // Create session here
    session_redirect("../index", ["_staffdata_error" => "Record Access Error: Could'nt retrieve record from datebase."]);
}

if ($staffData == false && !isAdmin()) {
    $id = $authUser["id"];
    $staffData = $staffDataController->staffDataModel->getRecordsBy("user_id", $id);
    if ($staffData == false) session_redirect("../create", ["_staff_data__info" => " One Step Close: We need just a couple of information inorder to continue, help us fill in below."]);
}

$imageCount = count($staffDataController->staffDataImagesModel->getRecordsBy("data_id", $staffData["id"], true));

?>
<link rel="stylesheet" href="<?= $appDirPath ?>assets/css/staff_images.css">
<!-- Begin page -->
<div id="layout-wrapper">
    <!-- ========== App Menu ========== -->
    <?php include dirname(__DIR__) . "/../includes/layouts/sidebar.php" ?>
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    <?php include dirname(__DIR__) . "/../includes/layouts/topbar.php" ?>
    <div class="main-content">
        <div class="page-content">
            <div class="container">
                <a href="<?= $appDirPath ?>staff_data/index" class="d-flex align-items-center gap-2"><i class="ph ph-arrow-left"></i><span>Go back</span></a>
                <div class="info-section my-2 d-flex align-items-center justify-content-between">
                    <div class="info-div">
                        <span class="text-dark bg-light p-1" style="border-radius: 5px;">Images Belongs To</span>
                        <h3 class="my-2"><?= $staffData['first_name'] . ' ' . $staffData['last_name'] ?> <?= $staffData['user_id'] == $authUser['id'] ? '(You)' : '' ?></h3>
                        <b class="bold">Has <?= $imageCount ? $imageCount : 'No' ?> Images</b>
                    </div>
                    <div class="position-relative">
                        <a href="add?id=<?= $staffData['user_id'] ?>" class="btn btn-sm btn-success d-flex align-items-center my-2" style="gap: 10px;">
                            <i class="mdi mdi-image-plus" style="font-size: 15px;"></i>
                            <span>Add Images</span>
                        </a>
                        <?php
                        $validExtensionsArray = array_map(function ($item) {
                            $item = '.' . $item;
                            return $item;
                        }, $staffDataController->imageHelper->getValidExtensions());

                        $acceptedExtensions = implode(", ", $validExtensionsArray);
                        ?>
                        <span class="text-danger">
                            <?= $staffDataController->handleNull("imageUpload", $staffDataController->fieldErrors) ?>
                        </span>
                    </div>
                </div>
                <?php if ($imageCount) : ?>
                    <div class="row">
                        <?php foreach ($staffDataController->paginatedImagesData($staffData['id']) as $key => $staffImageDataValue) : ?>
                            <div class="col-sm-3 my-2 card-container position-relative">
                                <div class="card">
                                    <img src="<?= $staffDataController->webImagePath . $staffImageDataValue['name'] ?>" alt="system-image:<?= $staffImageDataValue['date_created'] ?>" class="img-fluid system-image" preloaderId='system-image:<?= $staffImageDataValue['id'] ?>'>
                                </div>
                                <div class="top-content">
                                    <a href="view?id=<?= $staffImageDataValue['id'] ?>" class="btn btn-sm btn-success d-flex align-items-center" style="gap: 7px;">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="delete?id=<?= $staffImageDataValue['id'] ?>" class="btn btn-sm btn-danger d-flex align-items-center" style="gap: 7px;">
                                        <i class="bx bxs-trash"></i>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="centerDiv">
                        <span>
                            <?php $staffDataController->paginateImages($id) ?>
                        </span>
                    </div>
                <?php else : ?>
                    <div class="no-image-section w-100 d-flex align-items-center justify-content-center flex-column">
                        <div class="image-icon px-5 bg-light" style="border-radius: 20px;">
                            <i class="bi bi-images" style="font-size: 200px; color: #444;"></i>
                        </div>
                        <span class="text-secondary" style="margin: -50px;">
                            <b>You Have <?= $imageCount ? $imageCount : 'No' ?> Images</b>
                            <a href="add?id=<?= $staffData['user_id'] ?>" class="btn btn-sm btn-success d-flex align-items-center my-2" style="gap: 10px;">
                                <span>
                                    <i class="mdi mdi-image-plus" style="font-size: 15px;"></i>
                                </span>
                                <span>Add Images</span>
                            </a>
                            </form>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="/icuc_ars/app/assets/js/staff_images.js"></script>
<?php include_once dirname(__DIR__)."/../includes/footer.php"; ?>