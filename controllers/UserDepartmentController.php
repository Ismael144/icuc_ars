<?php 

namespace App\controllers;

use App\models\User;
use App\core\Controller;
use App\helpers\RequestDataHelper;
use App\models\UserDepartment;
use App\helpers\ValidationHelper;
 
class UserDepartmentController extends Controller {
    public UserDepartment $userDeptModel; 
    
    public User $userModel; 

    public array $departmentForm = [];

    public ValidationHelper $validateHelper;

    public function __construct() 
    {
        $this->validateHelper = new ValidationHelper;

        $this->userDeptModel = new UserDepartment; 

        $this->userModel = new User;
        
        if (RequestDataHelper::method('POST')) {
            $this->departmentForm = [
                "name" => $this->validateHelper->filter($this->handleNull("name", $_POST)),
                "description" => $this->validateHelper->filter($this->handleNull("description", $_POST)),
                "unassigned_users" => $this->handleNull("unassigned_users", $_POST),
            ];
        }
    }

    public function formValidation()
    {
        ["name" => $name, "unassigned_users" => $unassignedUsers] = $this->departmentForm; 

        if (empty($name)) {
            $this->setFieldError("name", "This field is required!");
        }
    }

    public function createDepartment(): bool 
    {
        $deptCreateData = $this->departmentForm; 

        // Check whether the created department is unique
        $results = $this->userDeptModel->getRecordsBy("name", $deptCreateData["name"], multiple: false);

        if ($results) {
            $this->setFieldError("name", "This department already exists!"); 
        }

        if ($this->noErrors()) {
            $results = $this->userDeptModel->create(["name" => $deptCreateData['name'], "description" => $deptCreateData['description']]);
            if ($results and count($deptCreateData['unassigned_users'])) {
                $createdDept = $this->userDeptModel->getRecordsBy('name', $deptCreateData['name']);
                // Assign the users 
                foreach($this->departmentForm['unassigned_users'] as $unassignedUserId) {
                    // Updated the user
                    $this->userModel->preparedUpdate(set: ['dept_id' => $createdDept['id']], where: ['id' => $unassignedUserId]);
                }
            }
            return true;
        }

        return false;
    }

    public function editDepartment(array $currentlyEditedDept): bool 
    {
        $deptEditData = $this->departmentForm; 

        // Check whether the created department is unique
        $results = $this->userDeptModel->getRecordsBy("name", $deptEditData["name"], multiple: false);

        if ($results && strtolower($this->departmentForm["name"]) != strtolower($currentlyEditedDept["name"])) {
            $this->setFieldError("name", "This department already exists!"); 
        }

        if ($this->noErrors()) {
            $results = $this->userDeptModel->edit(["name" => $deptEditData["name"], "description" => $deptEditData["description"], "id" => get('id')]);

            // If user in database 
            foreach($this->getUsersByDepartment(get('id')) as $userInDept) {
                if (!in_array($userInDept, $deptEditData['unassigned_users'])) {
                    // Updates users department info if is not in select
                    $this->userModel->preparedUpdate(['dept_id' => NULL], ['id' => $userInDept]);
                }
            }
            return true;
        }

        return false;
    }

    public function getUserDepartmentData($searchTerm = '')
    {
        $pageNumber = is_null(get('page')) ? 1 : get('page');
        $paginatedData = $this->userDeptModel->paginateData(pageNumber: $pageNumber, totalItemsPerPage: 6);
        return $paginatedData;
    }

    public function getUsersByDepartment(int $deptId): array 
    {
        $results = $this->userModel->getRecordsBy("dept_id", $deptId, multiple: true);
        return ["users_count" => count($results), "users" => $results];
    }

    public function deleteRecord(int $id): bool 
    {
        // Unlink user to department
        $this->userModel->preparedUpdate(["dept_id" => NULL], where: ["dept_id" => $id]);
        // Then delete afterwise
        return $this->userDeptModel->delete($id);
    }

    public function getUnassignedUsers(): array 
    {
        $authUser = AuthUserController::getAuthUser();
        $results = $this->userModel->runPreparedQuery("SELECT * FROM [model_table] WHERE dept_id IS NULL AND id != :authUserId", ["authUserId" => $authUser['id']], true, true);
        
        return $results;
    }
}