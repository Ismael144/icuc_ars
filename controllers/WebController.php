<?php

namespace App\controllers;

use App\models\User;
use App\traits\Utils;
use App\core\Controller;
use App\Database\Database;
use App\helpers\ImageHelper;
use App\helpers\SessionHelper;
use App\interfaces\ModelControllerInterface;

class WebController extends Controller
{
    public Database $db;

    public ImageHelper $imageHelper;

    public SessionHelper $sessionHelper;

    public string $uploadErrors = "";

    public UserController $userController;

    public User $userModel;

    use Utils;

    public function __construct()
    {
        $this->userModel = new User;
        $this->imageHelper    = new ImageHelper;
        $this->sessionHelper  = new SessionHelper;
        $this->userController = new UserController;

        // update notifications

        // Update read status of notifications older than one day
        $oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));
        $sql = "UPDATE notifications SET `is_read` = 1 WHERE `date_created` < :one_day_ago";
        $stmt = $this->userModel->usePDO()->prepare($sql);
        $stmt->execute(['one_day_ago' => $oneDayAgo]);
    }

    /**
     * Returns the version of mysql
     *
     * @return string
     */
    public function getSQLVersion()
    {
        $sqlVersion = $this->userModel->initDB()->sqlVersion;
        return $sqlVersion;
    }

    /**
     * Format bytes to quantity with units 
     *
     * @param int|float $bytes
     * @param integer $precision
     * @param boolean $withUnits
     * @return string|int
     */
    public static function formatBytes(int|float $bytes, $precision = 1, bool $withUnits = true): string|int
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return $withUnits ? round($bytes, $precision) . ' ' . $units[$pow] : round($bytes, $precision);
    }

    /**
     * Gets the disk total space and free space
     *
     * @return array
     */
    public function displayStorage($withUnits = true): array
    {
        $drive = "/";  // Specify the drive or directory you want to check

        $totalSpace = disk_total_space($drive);
        $freeSpace = disk_free_space($drive);
        // Format total and free space
        $totalSpaceFormatted = self::formatBytes($totalSpace, 2, $withUnits);
        $freeSpaceFormatted = self::formatBytes($freeSpace, 2, $withUnits);

        return ["free_space" => $freeSpaceFormatted, "total_space" => $totalSpaceFormatted];
    }


    /**
     * Displays table with all the functionality needed
     *
     * @param array $tableData Data from database, an associative array
     * @param string $tableName Optional, leave blank to use the exact table name, or enter it
     * @param array $configurations Contains configurations like, table columns to drop or not display, operations key, which will determine the operations to put on the table, 
     * 
     * Here is a code example
     * ```php
     * <?php 
     * 
     *  # Private method into the Utils trait in traits folder
     *  function format_date_time($timeFormat, $dateFormat): string
     * {
     *      // Your logic here .. 
     *  }
     * 
     * $webController = new WebController;
     * 
     *  $tableData = ['<key>' => '<value>']; 
     *  $tableName = 'Users';
     * 
     *  # Date Format 
     *  $date_format = "Y/m/d";
     * 
     *  # Time Format
     *  $time_format = "H:i:s";
     * 
     *  $configurations = [
     *      'keysToDrop' => ['password', 'avatar'], # Determine keys to drop in the table
     *      'operations' = [
     *          'edit' => true,    # The table will have an edit button
     *          'create' => true,  # Will have a create button
     *          'delete' => true,  # Will have a delete button
     *      ], 
     *      'date_and_time_created' => [ # This is a table column name, it will detect if a table column name exists and then applies the maps the function onto every value of that particular key
     *          'format_date_time', [$date_format, $time_format]
     *      ]
     *  ];
     * 
     *  $webController->outputTable($tableData, $tableName, $configurations);
     * 
     * ```
     * @return string
     */
    public function generateDataTable(array $tableData, string $tableName, $configurations = []): string
    {
        $tableHead = $tableBody = "";
        $idArray = [];

        # Configurations
        $configArrayKeys = array_keys($configurations);

        $editOperation   = true;
        $createOperation = true;
        $deleteOperation = true;

        # Working with all operations
        if (in_array("operations", $configArrayKeys)) {
            $createOperation = isset($configurations["operations"]["create"]) ? $configurations["operations"]["create"] : true;
            $deleteOperation = isset($configurations["operations"]["delete"]) ? $configurations["operations"]["delete"] : true;
            $editOperation = isset($configurations["operations"]["edit"]) ? $configurations["operations"]["edit"] : 'true';
        }

        if (!count($tableData)) {
            return <<<"HTML"
                <div style="height: 300px; width: 100%; border-radius: 10px;" class="card bg-white border my-2">
                    <div class="card-body d-flex flex-column gap-2 align-items-center justify-content-center">
                        <b style="font-size: 16px;">No Items Found In The Table</b>
                        <a href="create" class="btn btn-success my-3">Create New</a>
                    </div>
                </div>
            HTML;
        }


        $keysToDrop = in_array("keysToDrop", $configArrayKeys) ? $configurations["keysToDrop"] : [];
        // $withOperations = in_array("withOperations", $configArrayKeys) ? $configurations["withOperations"] : true;

        if (!count($tableData)) return "The <b>'$tableName'</b> table is empty";

        for ($i = 0; $i < count($tableData); $i++) {
            # Assigning the real ids from the table to the idArray
            # Its done so, to avoid skipped numbers, e.g. when a record is deleted, would be like 2, 4,5, 6 when item with id - 3 is deleted 
            $idArray[$i]['id'] = $tableData[$i]['id'];
            # Setting an organized id e.g. 1,2,3,4
            $tableData[$i]['id'] = $i + 1;

            # Will drop keys that are set in the drop $keyToDrop array
            foreach ($keysToDrop as $dropKey) {
                if (in_array($dropKey, array_keys($tableData[$i]))) {
                    unset($tableData[$i][$dropKey]);
                }
            }

            $arrayKeys = array_keys($tableData[$i]);

            foreach ($arrayKeys as $_ => $key) {
                # Checks if a key exists in the configurations associative array, if true then runs the function on to that data for example 'date_created' => format_date($data); the logic in form of ['<function_name>', [...<args>]], but the functions called will come from this class(WebController). All functions to use can be put into the Utils trait
                if (in_array($key, $configArrayKeys)) {
                    if (!is_callable($configurations[$key])) {
                        # Parses functions
                        $args = isset($configurations[$key][1]) ? $configurations[$key][1] : [];
                        $tableData[$i][$key] = call_user_func(
                            [self::class, $configurations[$key][0]],
                            $tableData[$i][$key],
                            ...$args
                        );
                    } else {
                        $tableData[$i][$key] = $configurations[$key]($tableData[$i][$key]);
                    }
                }
            }

            $arrayValues = array_values($tableData[$i]);
            $tableId = $this->handleNull("id", $idArray[$i]);

            # Table Oerations
            $refreshButton = <<<"HTML"
                <a href="" class='btn btn-sm btn-dark d-flex align-items-center' style='gap: 5px;'><span><i class='mdi mdi-refresh'></i></span><span>Refresh</span></a>
            HTML;

            $createButton = $createOperation ? <<<"HTML"
                <a href='create' class='btn btn-primary btn-sm d-flex align-items-center' style='gap: 5px;'><span><i class='mdi mdi-plus'></i></span><span>Create</span></a>
            HTML : "";

            $deleteButton = $deleteOperation ? <<<"HTML"
                <a href="delete?id=$tableId" class="btn btn-sm btn-danger" style='border-radius: 50% !important;'><i class="mdi mdi-trash-can-outline" style="font-size: 15px !important;"></i></a> 
            HTML : "";

            $editButton = $editOperation ? <<<"HTML"
                <a href="edit?id=$tableId" class="btn btn-sm btn-success" style='border-radius: 50% !important;'><i class="mdi mdi-square-edit-outline" style="font-size: 15px !important;"></i></a>        
            HTML : "";

            foreach ($arrayValues as $value) {
                $value = empty($value) ? "None" : $value;
                $tableBody .= "<td>$value</td>";
            }

            $tableBody .= $editOperation == true || $deleteOperation == true ? <<<"HTML"
                    <td style="width: 120px;">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Edit Button -->
                            $editButton
                            <!-- Delete Button -->
                            $deleteButton
                        </div>
                    </td>
                </tr>
            HTML : <<<"HTML"
                </tr>
            HTML;
        }

        # Removing underscores and capitalizing the column names to be displayed in table
        $arrayKeys = array_map(function ($key) {
            return ucwords(str_replace("_", " ", $key));
        }, $arrayKeys);

        foreach ($arrayKeys as $i => $key) {
            $tableHead .= "<th>$key</th>";
        }

        # Looping through the array keys of arrayData array
        $tableHead .= $editOperation == true || $deleteOperation == true ? "<th>Operations</th>" : "";

        return <<<"HTML"
            <div class="nav-btn my-3 d-flex align-items-center justify-content-end" style='gap: 10px;'>
                <!-- Refresh button -->
                {$refreshButton}
                <!-- Create button -->
                {$createButton}
            </div>
            <table id="$tableName-datatable" class="display table table-bordered table-striped" style="width: 100%; margin: 3px; border-radius: 5px !important;">
                <thead>
                    <tr>{$tableHead}</tr>
                </thead>
                <tbody> {$tableBody}</tbody>
                <thead>
                    <tr>{$tableHead}</tr>
                </thead>
            </table>
            <script>
            var a = $("#$tableName-datatable").DataTable({
                    language: {
                        paginate: {
                            previous: "<i class='mdi mdi-chevron-left'>",
                            next: "<i class='mdi mdi-chevron-right'>"
                        }
                    },
                    drawCallback: function() {
                        $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
                    }
                });
            </script>
        HTML;
    }

    /**
     * Displays the index page of a model
     *
     * @param ModelControllerInterface $modelController
     * @param string $includesPath
     * @param string $displayName
     * @param array $notAllowedTableRows
     * @return void
     */
    public function RenderIndexPage(ModelControllerInterface $modelController, string $displayName = "", $configurations = [])
    {
        $tableName = $modelController->getTableName();
        $displayName = strlen($displayName) ? $displayName : ucwords($tableName);

        $tableData = !empty($username) ? $modelController->getTableData() : $modelController->getTableData();


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
                                        <h4>$displayName</h4>
                                        <p class="text-muted mb-4">{$modelController->getTableRecordsCount()} records in the database</p>
                                        <a href="users/index.php" class="d-flex align-items-center gap-3">
                                            For more info
                                            <i class="ph ph-arrow-right" style="font-size: 18px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive $displayTableName-table mt-5">
                        {$this->generateDataTable($tableData,$tableName,$configurations)}
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * Displays sweet alert notifications
     * Should contain the following keys 
     * -> title, icon, button, confirmButtonClass, text, key 
     *
     * @param array $params
     * @return null|boolean
     */
    public function alert(string $sessionKey, array $alertParams)
    {
        ["title" => $title, "icon" => $icon, "description" => $description, "alertClass" => $alertClass] = $alertParams;

        $alertHTML = <<<"HTML"
            <script> 
                Toastify({
                    title: "{$title}",
                    duration: 3550,
                    description: "{$description}",
                    close: true,
                    icon: "{$icon}",
                    className: "{$alertClass}",
                    offset: {
                        x: 9,
                        y: 57,
                    },
                }).showToast();
            </script>
        HTML;

        if (isset($_SESSION[$sessionKey])) {
            echo $alertHTML;
            unset($_SESSION[$sessionKey]);
        }
    }

    /**
     * Handles session alerts 
     * Session Naming Convention => _modelName__operation e.g. _user__create
     * 
     * @return void
     */
    public function handleAllAlertSessions()
    {
        foreach ($_SESSION as $key => $value) {
            if (str_starts_with($key, "_")) {
                $splitKey = explode("__", $key);
                $operation = strtolower(end($splitKey));

                $explodedValue = explode(":", $value);
                $title = $explodedValue[0];
                $description = end($explodedValue);

                $icon = [
                    "success" => "<i class='fas fa-check'></i>",
                    "error" => "<i class='fas fa-times'></i>",
                    "info" => "<i class='bi bi-exclamation-circle-fill'></i>",
                    "warning" => "<i class='bi bi-exclamation-circle-fill'></i>",
                ];

                $alertParams = [
                    "title" => $title,
                    "description" => "<span style='font-size: 15px !important;'>$description</span>",
                    "alertClass" => "success-bg",
                    "button" => "ok",
                    "icon" => $icon["success"]
                ];

                switch ($operation) {
                    case "success":
                        $alertParams['title'] = "{$title}";
                        $this->alert($key, $alertParams);
                        break;

                    case "error":
                        $alertParams['title'] = "{$title}";
                        $alertParams['icon'] = $icon[$operation];
                        $alertParams['alertClass'] = "danger-bg";
                        $this->alert($key, $alertParams);
                        break;

                    case "notice":
                        $alertParams['title'] = "Notice";
                        $alertParams['icon'] = $icon["warning"];
                        $alertParams['alertClass'] = "warning";
                        $this->alert($key, $alertParams);
                        break;

                    case "warning":
                        $alertParams['icon'] = $icon["warning"];
                        $alertParams['alertClass'] = "warning";
                        $this->alert($key, $alertParams);
                        break;

                    case "info":
                        $alertParams['icon'] = $icon["info"];
                        $alertParams['alertClass'] = "info";
                        $this->alert($key, $alertParams);
                        break;
                }
            }
        }
    }
}
