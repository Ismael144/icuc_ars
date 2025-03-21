<?php
$pageTitle = "User Analysis";
include dirname(__DIR__) . "/includes/header.php" ?>

<?php

use App\controllers\SystemCleanupController;

$sysCleanupController = new SystemCleanupController(dirname(__DIR__) . "/../images");

$sysCleanupController->cleanupUserImageRecordsInDatabase();
$sysCleanupController->cleanupStaffDataImageRecordsInDB();
$sysCleanupController->removeDanglingStaffDataRecords();

adminAuthProtect("../dashboard");

?>

<?php
include dirname(__DIR__) . "/includes/layouts/topbar.php";
include dirname(__DIR__) . "/includes/layouts/sidebar.php";
?>

<div class="main-content">
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <h4 class="my-3">System Cleanup Service</h4>
                <div class="system-cleanup-diagnosis-container" id="scd-service">
                    <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
                        <div>
                            <i class="fas fa-server text-success" style="font-size: 70px;"></i>
                        </div>
                        <div class="content my-4">
                            <b>Welcome To The System Cleanup and diagnosis service</b>
                        </div>
                        <div>
                            <button class="btn btn-success" id="run_checkup">Run Checkup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= $appDirPath ?>assets/js/systemCleanupService.js"></script>
<?php include dirname(__DIR__) . "/includes/footer.php" ?>