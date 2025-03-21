<?php

namespace App\models;

use App\controllers\AuthUserController;
use App\core\Model;
use App\enums\{UserStatus, UserRole};
use App\interfaces\ModelInterface;

class User extends Model implements ModelInterface
{
    public static string $passwordHashingAlgorithm = PASSWORD_BCRYPT;

    public UserDepartment $userDeptModel;

    private ?array $authUser = [];

    /**
     * 
     * @var string $staticTableName 
     * 
     * This is done like this, so that the authenticated user can be accessed without instantiating a the AuthUserController class
     */ 
    public static string $staticTableName = "users";

    public static string $rolesTableName = "roles";

    public static string $departmentsTableName = "user_departments";


    public function __construct(
        public string $tableName = "users"
    ) {
        $this->authUser = AuthUserController::getAuthUser();
        $this->userDeptModel = new UserDepartment; 
    }

    public function getRoleById(string | int $userId)
    {
        $results = $this->runQuery("SELECT role FROM roles WHERE id = $userId", true, false);
        return $results['role'];
    }

    public function updateLastLogin(string $userId)
    {
        $current_datetime = date("Y-m-d H:i:s");
        $this->runQuery("UPDATE {$this->tableName} SET last_login = '$current_datetime' WHERE id = $userId", false);
    }

    public function checkPasswordIfValid(string $email, string $password)
    {
        # Querying the password by user's email
        $stmt = $this->usePDO()->prepare("SELECT id, password FROM {$this->tableName} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(0);

        # If the user with given email exists
        if ($stmt->rowCount()) {
            # Get the hashed password from the database
            $hashedPassword = $user['password'];
            if (password_verify($password, $hashedPassword)) {
                return true;
            }
            return false;
        }
    }
    
    /**
     * This method takes in roles and allows user to access the roles depending on their role
     * 
     * ```php 
     * <?php 
     *  # Refers to all admin roles, meaning even though 
     *  # they are admin or super admin, will be able to access
     *  $roles = []; 
     *  $allowedByRoles = $userModel->allowByRoles('__admins__'); 
     * 
     *  # Then some if logic here
     * 
     * ```
     * 
     * @param UserStatus ...$userRoles 
     * @return bool 
     */
    public function allowByRoles(UserRole ...$userRoles): bool 
    {
        $userRoles = array_map(function(UserRole $item) {
            return $item->value; 
        }, $userRoles);

        return in_array($this->authUser['role_id'], $userRoles);
    }

    public function getUsersTableData()
    {
        $username = $this->authUser['username'];

        $isAuthUserAdmin = $this->allowByRoles(UserRole::STAFF_MEMBER);

        $additionalQuery = $isAuthUserAdmin ? "AND role_id != " . UserRole::SYSTEM_ADMINISTRATOR->value . " AND role_id != " . UserRole::STAFF_MEMBER->value : "";

        $tableData = $this->runQuery("
            SELECT u.id as id,
            u.username,
            u.email,
            u.phone_number,
            u.gender,
            u.status,
            r.role,
            u.dept_id as department,
            u.last_login,
            u.date_created
            FROM {$this->tableName} as u
            JOIN roles AS r
            ON u.role_id = r.id WHERE username != '{$username}' $additionalQuery
        ", true, true);

        $tableData = array_map(function($item) {
            $item["department"] = is_null($item["department"]) ? "Not Registered" : ucwords($this->userDeptModel->getById($item["department"])["name"]);
            $item['status'] = $item['status'] == 1 ? "Active" : "Inactive";
            return $item; 
        }, $tableData);

        return $tableData;
    }

    /**
     * Updates status from 0 to 1
     * 0 means Inactive
     * 1 means Active
     *
     * @param UserStatus $status
     * @param string $uniqid
     * @return bool
     */
    public function updateStatus(UserStatus $status, string $uniqid = ""): bool
    {
        $authUserId = $this->authUser ? $this->authUser['id'] : $this->getRecordsBy("uniqid", $uniqid, false)['id'];
        $this->runQuery("UPDATE {$this->tableName} SET status = {$status->value} WHERE id = $authUserId", false, false);

        return true; 
    }

    public function createUser(array $arrayData)
    {
        return $this->doInsert($arrayData);
    }

    public function updateUser(array $arrayData)
    {
        ["username" => $username, "email" => $email, "role" => $role, "password" => $password, "phone" => $phone_number, "avatar" => $avatarField, "id" => $id] = $arrayData;

        $stmt = $this->usePDO()->prepare("UPDATE {$this->tableName} SET username = :username, email = :email, role_id = :role, password = :password, phone_number = :phone, avatar = :avatar WHERE id = :id");

        $stmt->execute(["username" => $username, "email" => $email, "role" => $role, "password" => $password, "phone" => $phone_number, "avatar" => $avatarField, "id" => $id]);
        return true;
    }

    /**
     * Updates password of particular user
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function passwordResetByEmail(string $email, string $password): bool
    {
        $results = $this->runQuery("SELECT count(email) as email_count FROM {$this->tableName} WHERE email = '$email'");

        if (!$results['email_count']) return false;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $results = $this->runQuery("UPDATE {$this->tableName} SET password = '$hashedPassword'", false);
        return true;
    }
}
