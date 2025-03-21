<?php

$pageTitle = "Staff Data";
include_once dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

if (!isAdmin())
    $webController->redirect("images/index?id={$authUser['id']}");

use App\controllers\StaffDataController;

$staffDataController = new StaffDataController;
$notAllowedTableRows = ["password", "avatar"];

/* Renders the index page of a model */

$tableName = $staffDataController->getTableName();

$tableData = $staffDataController->getTableData();


$displayTableName = ucwords($tableName);

// Just for fun, change to random colors on refresh
$randomColors = ["#0035ff8a", "orange", "#2dcb73", "#f340088a", "#ffa5008a", "indigo"];
$randomColor = $randomColors[rand(0, count($randomColors) - 1)];

echo <<<"HTML"
        <div class="main-content">
        <div class="page-content mx-3">
            <div class="table-info">
                <div class="col-xxl col-3">
                    <div class="card overflow-hidden py-2 border-translucent" style="border-left: 5px solid $randomColor; width: 20rem !important;">
                        <div class="card-body position-relative d-flex align-items-center gap-2 justify-content-between">
                            <div class="left-content">
                                <h4>Staff Data Table</h4>
                                <p class="text-muted mb-4">{$staffDataController->getTableRecordsCount()} records in the database</p>
                                <a href="users/index.php" class="d-flex align-items-center gap-3">
                                    For more info
                                    <i class="ph ph-arrow-right" style="font-size: 18px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="$displayTableName-table mt-5">
                <a href="images/index?id={$authUser['id']}" class="btn btn-success d-flex align-items-center gap-3" style="display: inline-flex !important;">
                    <div><span>
                        Your Profile
                    </span></div>
                    <div>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                {$webController->generateDataTable($tableData, $tableName, configurations: [
                    'keysToDrop' => ['user_id'],
                    'date_created' => ['format_date'],
                    'role' => ['_capitalize'],
                ])}
            </div>
        </div>
    </div>
HTML;



include dirname(__DIR__) . "/includes/layouts/sidebar.php";
include dirname(__DIR__) . "/includes/layouts/topbar.php";

include_once dirname(__DIR__) . "/includes/footer.php";