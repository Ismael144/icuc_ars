<?php

namespace App\controllers;

use App\models\User;
use App\core\Controller;
use App\models\StaffData;
use App\helpers\ImageHelper;
use App\models\StaffDataImage;
use App\helpers\ValidationHelper;
use App\interfaces\ModelControllerInterface;

class StaffDataController extends Controller implements ModelControllerInterface
{
    public StaffData $staffDataModel;

    public StaffDataImage $staffDataImagesModel;

    public array $staffDataForm = [];


    public ImageHelper $imageHelper;

    public ValidationHelper $validateHelper;

    public string $imagePath = "";

    public array $fieldErrors = [];

    # Pagination Properties 
    public int $currentPage = 1;

    public int $totalPages = 1;

    public int $totalItemsPerPage = 4;

    public WebController $webController;

    public User $userModel;

    public string $webImagePath = "/icuc_ars/images/staff_images/";

    public function __construct()
    {
        $this->userModel = new User;
        $this->imageHelper    = new ImageHelper;
        $this->staffDataImagesModel = new StaffDataImage;
        $this->validateHelper = new ValidationHelper;
        $this->webController  = new WebController;

        $this->imagePath = dirname(__DIR__) . "/images/staff_images/";

        if ($_SERVER['REQUEST_METHOD'] == "POST")
            $this->staffDataForm = [
                "staff"        => $this->validateHelper->filter($this->handleNull("staff", $_POST)),
                "first_name"   => $this->validateHelper->filter($this->handleNull("first_name", $_POST)),
                "last_name"    => $this->validateHelper->filter($this->handleNull("last_name", $_POST)),
            ];

        $this->staffDataModel = new StaffData;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->staffDataModel->tableName;
    }

    public function fetchColumn(string $columnName)
    {
        $pdo = $this->staffDataModel->usePDO();
        $stmt = $pdo->query("SELECT $columnName FROM {$this->staffDataModel->tableName}");
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Get data from table
     * Only use this method when using the `generateDataTable` method from the `WebController` class
     * Its not ideal to use it in other places
     *
     * @return array
     */
    public function getTableData(): array
    {
        $pdo = $this->staffDataModel->usePDO();
        $stmt = $pdo->query("SELECT id, concat(first_name, ' ', last_name) as 'Full Name', user_id FROM {$this->staffDataModel->tableName}");
        $results = $stmt->fetchAll();

        $results = array_map(function ($item) {
            $userInfo = $this->userModel->getRecordsBy("id", $item["user_id"], what: ["id", "username as 'belongs_to'", "email", "phone_number"]);

            if ($userInfo["id"] == AuthUserController::getAuthUser()["id"]) {
                $userInfo["belongs_to"] .= " (You)"; 
            }
            // Drop id to avoid confusion between user's id from staff_data item id and userInfo
            unset($userInfo["id"]);

            $item = [...$item, ...$userInfo];

            $numberOfImages = count($this->staffDataImagesModel->getRecordsBy("data_id", $item["id"], true));
            $item['images'] = <<<"HTML"
                <div class='d-flex align-items-center justify-content-between'>
                    <span class='text-muted'>{$numberOfImages} Images</span> 
                    <a href='images/index?id={$item["user_id"]}' style='text-decoration: underline; border-radius: 50% !important;' class='btn btn-sm btn-primary' title='Click to view images that belong to a particular record'><i class='bi bi-plus' style='font-size: 16px; border-radius: 50% !important;'></i></a>
                </div>
            HTML;

            return array_merge($item, $userInfo);
        }, $results);

        return $results;
    }


    /**
     * Get the number of rows in a table
     *
     * @return integer|boolean
     */
    public function getTableRecordsCount(): int|bool
    {
        return $this->staffDataModel->getTableRecordsCount();
    }


    public function createRecord()
    {
        global $firstName, $lastName, $staffUserId;
        global $authUser;

        $staffUserId = isAdmin() ? $staffUserId : $authUser["id"];

        # Some Validations 
        $fullName = $firstName . ' ' . $lastName;

        if (!$this->checkFullNameExistence("", $fullName, true)) $this->fieldErrors["fullName"] = "'$fullName' is not available, please use another";

        $user = $this->userModel->getRecordsBy("id", $staffUserId, false);
        if ($this->staffDataModel->makeCountQueriesOfTable("user_id", $staffUserId)) $this->fieldErrors["staff"] = "User '{$user["username"]}' was already created...";

        if ($this->noErrors()) {
            # The Insertion of data in database 
            // Checks whether the logged in person is a system administrator
            $stmt = $this->staffDataModel->usePDO()->prepare("INSERT {$this->staffDataModel->tableName}(first_name, last_name, user_id) VALUES (:fName, :lName, :user_id)");
 
            $stmt->execute(["fName" => $firstName, "lName" => $lastName, "user_id" => $staffUserId]);

            return true;
        }

        return false;
    }

    public function checkFullNameExistence(string $currentlyUpdatedRecord, string $fullName, bool $isInsert = false)
    {
        $pdo = $this->staffDataModel->usePDO();

        if (!$isInsert) {
            $stmt = $pdo->prepare("SELECT count(id) as record_count FROM {$this->staffDataModel->tableName} WHERE concat(first_name, ' ', last_name) != ? AND concat(first_name, ' ', last_name) = ?");
            $stmt->execute([$currentlyUpdatedRecord, $fullName]);
            $results = $stmt->fetch();
            return $results['record_count'] ? false : true;
        }

        $stmt = $pdo->prepare("SELECT count(id) as record_count FROM {$this->staffDataModel->tableName} WHERE concat(first_name, ' ', last_name) = ?");
        $stmt->execute([$fullName]);
        $results = $stmt->fetch();
        return $results['record_count'] ? false : true;
    }

    public function updateDataRecord(array $selectedRecord)
    {
        # Records from the form input 
        $firstName = strlen($this->handleNull('first_name', $this->staffDataForm)) ? $this->handleNull('first_name', $this->staffDataForm) : $this->handleNull("first_name", $selectedRecord);
        $lastName = strlen($this->handleNull('last_name', $this->staffDataForm)) ? $this->handleNull('last_name', $this->staffDataForm) : $this->handleNull("last_name", $selectedRecord);
        $staffId = strlen($this->handleNull('staff', $this->staffDataForm)) ? $this->handleNull('staff', $this->staffDataForm) : $this->handleNull("staff", $selectedRecord);

        $fullName = $this->handleNull("first_name", $this->staffDataForm) . " " . $this->handleNull("last_name", $this->staffDataForm);
        $fullNameForCurrentlyUpdatedRecord = $selectedRecord["first_name"] . " " . $selectedRecord["last_name"];

        // Performing duplication checks
        if (!$this->checkFullNameExistence($fullNameForCurrentlyUpdatedRecord, $fullName)) $this->fieldErrors['fullName'] = "Name '$fullName' is not available, please use another";

        // Preventing user staff data duplication
        $user = $this->userModel->getRecordsBy("id", $staffId, false);
        if ($this->staffDataModel->makeCountQueriesOfTable("user_id", $staffId) && $selectedRecord["user_id"] != $staffId) $this->fieldErrors["staff"] = "User '{$user["username"]}' was already chosen, please try out others...";


        if (!count($this->fieldErrors)) {
            # Performing Update Query
            $sysData = ["first_name" => $firstName, "last_name" => $lastName, "id" => $selectedRecord["id"], "staff_user_id" => $staffId];
            $this->staffDataModel->update($sysData);

            return true;
        }
        return false;
    }

    public function deleteRecord(int $id)
    {
        $recordImages = $this->staffDataImagesModel->getRecordsBy("data_id", $id, true);
        $recordToDelete = $this->staffDataModel->getRecordsBy("id", $id);
        $fullName = $recordToDelete["first_name"] . " " . $recordToDelete["last_name"];
        $this->staffDataModel->deleteItemFromTable("id", $id);

        // Also deletes their images 
        foreach ($recordImages as $recordImage) {
            @unlink($this->imagePath . $recordImage);
        }

        return true;
    }

    public function formValidation()
    {
        if (strlen($this->handleNull("first_name", $this->staffDataForm))) {
            if (!$this->validateHelper->isUsernameValid($this->handleNull("first_name", $this->staffDataForm))) $this->fieldErrors['first_name'] = "The first name contains invalid characters make sure it does not contain characters like '$@#$%^'";
        } else $this->fieldErrors['first_name'] = "This field is required!";

        if (strlen($this->handleNull("first_name", $this->staffDataForm))) {
            if (!$this->validateHelper->isUsernameValid($this->handleNull("first_name", $this->staffDataForm))) $this->fieldErrors['last_name'] = "The last name contains invalid characters make sure it does not contain characters like '$@#$%^'";
        } else $this->fieldErrors['last_name'] = "This field is required!";

        if (isAdmin()) {
            if (!empty($this->handleNull("staff", $this->staffDataForm))) {
                // if staff id is valid
                if (!$this->isStaffIdValid($this->handleNull("staff", $this->staffDataForm))) {
                    $this->fieldErrors["staff"] = "Crap, the staff id is invalid...";
                }
            } else {
                $this->fieldErrors["staff"] = "This field is required!";
            }
        }
    }

    private function isStaffIdValid(int|string $staffId): bool
    {
        $results = $this->userModel->runQuery("SELECT count(*) as user_count FROM {$this->userModel->tableName} WHERE id = $staffId");
        return $results["user_count"] ? true : false;
    }

    /**
     * Returns the number of images each system_data reord has
     *
     * @param integer|string $data_id
     * @return int|bool
     */
    public function getStaffDataImagesRecordCount(int|string $data_id): int|bool
    {
        $pdo = $this->staffDataModel->usePDO();
        $stmt = $pdo->query("SELECT count(*) as image_count FROM {$this->staffDataImagesModel->tableName} WHERE data_id = '$data_id'");
        $results = $stmt->fetch();
        return $results['image_count'];
    }

    /**
     * Adds pagination to the rendered images
     *
     * @param integer $currentRecordId
     * @return void
     */
    public function paginateImages(int|string $currentRecordId)
    {
        echo <<<"HTML"
            <ul class="pagination mb-0">
        HTML;

        if ($this->currentPage > 1) {
            $previousPage = $this->currentPage - 1;
            echo <<<"HTML"
                <li class="page-item"><a class="page-link" href="?id={$currentRecordId}&page={$previousPage}"><i class="fas fa-chevron-left"></i></a></li>
            HTML;
        }

        for ($i = 1; $i <= $this->totalPages; $i++) {
            $paginationClass = $this->currentPage == $i ? "active" : "";
            echo <<<"HTML"
                    <li class="page-item {$paginationClass} mx-1">
                        <a class="page-link" href="?id={$currentRecordId}&page={$i}">$i</a>
                    </li>
                HTML;
        }

        if ($this->currentPage < $this->totalPages) {
            $nextPage = $this->currentPage + 1;
            echo <<<"HTML"
                    <li class="page-item"><a class="page-link" href="?id=$currentRecordId&page=$nextPage"> <i class="fas fa-chevron-right"></i> </a></li>
                HTML;
        }

        echo <<<"HTML"
            </ul>
        HTML;
    }

    public function paginatedImagesData(int|string $dataId)
    {
        $pdo = $this->staffDataModel->usePDO();
        $imagesTableCount = $this->getStaffDataImagesRecordCount($dataId);
        $total_items = $imagesTableCount;
        $this->totalPages = ceil($total_items / $this->totalItemsPerPage);
        $this->currentPage = isset($_GET['page']) && $this->totalPages >= $_GET['page'] ? $_GET['page'] : 1;
        $offset = ($this->currentPage - 1) * $this->totalItemsPerPage;
        $result = $pdo->prepare("SELECT * FROM {$this->staffDataImagesModel->getTableName()}
            WHERE data_id = :dataId ORDER BY date_created LIMIT :offset, :items_per_page");
        $result->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $result->bindParam(':items_per_page', $this->totalItemsPerPage, \PDO::PARAM_INT);
        $result->bindParam(':dataId', $dataId);

        $result->execute();
        $items = $result->fetchAll();

        return $items;
    }

    public function getDataWithImages()
    {
        $data = $this->staffDataModel->getPartialData();

        $data = array_map(function ($item) {
            $item["images"] = array_map(function ($image) {
                return "http://localhost/icuc_ars/images/staff_images/" . $image['name'];
            }, (new StaffDataImage)->getImagesBy($item["id"]));
            return $item;
        }, $data);

        return $data;
    }

    public function getDataWithImagesForSingleById($id): array | false
    {
        $singleRecord = $this->staffDataModel->getRecordsBy("id", $id);

        if ($singleRecord == false || $singleRecord == []) {
            return false;
        }

        $singleRecord["images"] = array_map(function ($image) {
            return "http://localhost/icuc_ars/images/staff_images/" . $image['name'];
        }, (new StaffDataImage)->getImagesBy($singleRecord["id"]));

        return $singleRecord;
    }
}
