<?php

namespace App\controllers;

use App\models\User;
use App\core\Controller;
use App\enums\UserRole;
use App\helpers\{ValidationHelper, SessionHelper, ImageHelper, RequestDataHelper};
use App\interfaces\ModelControllerInterface;

class UserController extends Controller implements ModelControllerInterface
{
    public ImageHelper $imageHelper;

    public SessionHelper $sessionHelper;

    public ValidationHelper $validateHelper;

    public UserActivitiesController $userActivitiesController;

    public UserDepartmentController $userDeptController; 

    public array $fieldErrors;

    public array $user = [];

    public string $imageDestination = "/icuc_ars/images/users/";

    public string $imagePath = "";

    public User $userModel;

    public function __construct()
    {
        $this->fieldErrors = [];
        $this->userModel = new User;
        $this->sessionHelper = new SessionHelper;
        $this->validateHelper = new ValidationHelper;
        $this->imagePath = dirname(__DIR__) . "/images/staff_images/";
        $this->imageHelper = new ImageHelper($this->imageDestination);
        $this->userDeptController = new UserDepartmentController; 

        if (RequestDataHelper::method('POST'))
            $this->user = [
                "username"         => $this->validateHelper->filter($this->handleNull('username', $_POST)),
                "email"            => $this->validateHelper->filter($this->handleNull('email', $_POST)),
                "password"         => $this->validateHelper->filter($this->handleNull('password', $_POST)),
                "role"             => $this->validateHelper->filter($this->handleNull('role', $_POST)),
                "department"       => $this->validateHelper->filter($this->handleNull('department', $_POST)),
                "phone_number"     => $this->validateHelper->filter($this->handleNull('phone_number', $_POST)),
                "gender"     => $this->validateHelper->filter($this->handleNull('gender', $_POST)),
                "avatar"           => $this->handleNull('avatar', $_FILES),
                "verificationCode" => $this->validateHelper->filter($this->handleNull('verificationCode', $_POST)),
            ];
    }

    /**
     * Returns the name of the table 
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->userModel->tableName;
    }

    /**
     * Returns the number of records or rows in a table 
     *
     * @return integer|boolean
     */
    public function getTableRecordsCount(): int|bool
    {
        return count($this->getTableData());
    }

    public function getUserInfoById(int $userId): array|bool
    {
        $userInfo = $this->userModel->getRecordsBy('id', $userId); 

        $userInfo['department'] = $this->userDeptController->userDeptModel->getRecordsBy('id', $userInfo['dept_id'], what: ['name'])['name'];
        $userInfo['role'] = $this->userModel->getRoleById($userInfo['role_id']);

        return $userInfo; 
    }

    /**
     * Returns Data from the users table
     *
     * @param string $username
     * @return array<mixed>
     */
    public function getTableData(): array
    {
        return $this->userModel->getUsersTableData();
    }
    private function checkUsernameExistence(string $currentlyUpdatedUser, string $usernameForEditing)
    {
        $signedInUser = AuthUserController::getAuthUser()['username'];
        $pdo = $this->userModel->usePDO();
        $stmt = $pdo->prepare("SELECT count(id) as user_count FROM {$this->userModel->tableName} WHERE username != :username1 AND username != :username2 AND username = :username3");
        $stmt->execute(["username1" => $signedInUser, "username2" => $currentlyUpdatedUser, "username3" => $usernameForEditing]);
        $results = $stmt->fetch();

        return $results['user_count'] ? false : true;
    }

    /**
     * Updates users table by id 
     * Works by checking if credentials like username and email are not duplicate
     * if Not then performs the operations 
     *
     * @param string $avatarName
     * @param array $user
     * @return ?bool
     */
    public function updateUser(?string $avatarName = "", array $currentlyUpdatedUser): ?bool
    {

        $username = $this->handleNull('username', $this->user) ?: $this->handleNull("username", $currentlyUpdatedUser);
        $email = $this->handleNull('email', $this->user) ?: $this->handleNull('email', $currentlyUpdatedUser);
        $password = $this->handleNull('password', $this->user) ?: $this->handleNull('password', $currentlyUpdatedUser);
        $role = $this->handleNull('role', $this->user) ?: $this->handleNull('role_id', $currentlyUpdatedUser);
        $department = $this->handleNull('department', $this->user) ?: $this->handleNull('dept_id', $currentlyUpdatedUser);
        $phone_number = $this->handleNull('phone_number', $this->user) ?: $this->handleNull('phone_number', $currentlyUpdatedUser);
        $gender = $this->handleNull('gender', $this->user) ?: $this->handleNull('gender', $currentlyUpdatedUser);
        $avatarField = strlen($avatarName) ? $avatarName : $currentlyUpdatedUser['avatar'];

        # Checking whether the password 
        if (!$this->verifyPasswordAlgorithm($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        }

        if (is_array($this->user['avatar'])) {
            $this->imageHelper->uploadImage($this->user['avatar'], dirname(__DIR__) . "/images/users/", $currentlyUpdatedUser['avatar']);
        }

        # Check whether the username and email are in database 
        if (!$this->checkUsernameExistence($currentlyUpdatedUser['username'], $this->handleNull("username", $this->user))) {
            $this->fieldErrors['username'] = "Username '$username' is already taken, please try another one";
        }

        if (AuthUserController::getAuthUser()['role_id'] == UserRole::SYSTEM_ADMINISTRATOR && $this->user['role'] == UserRole::SYSTEM_ADMINISTRATOR) $this->fieldErrors['role'] = "You have no privileges of registering a super admin yet you are an admin.";

        if ($currentlyUpdatedUser['email'] != $this->handleNull("email", $this->user) and $this->userModel->makeCountQueriesOfTable("email", $this->handleNull('email', $this->user))) {
            $this->fieldErrors['email'] = "Email '$email' is already taken, please try another one";
        }

        if (count($this->fieldErrors)) {
            return false;
        }

        /* Update query here */
        $this->userModel->preparedUpdate(set: ["username" => $username, "email" => $email, "password" => $password, "phone_number" => $phone_number, "gender" => $gender, "role_id" => $role, "dept_id" => $department, "avatar" => $avatarField], where: ["id" => $currentlyUpdatedUser['id']]);

        return true;
    }

    public function genderMapper(?string $genderVal): string 
    { 
        return match($genderVal) {
            "0" => "Male", 
            "1" => "Female", 
            "2" => "Custom", 
            default => "Not Set"
        };
    }

    public function formValidation()
    {
        $username = $this->handleNull("username", $this->user);
        $email = $this->handleNull("email", $this->user);
        $password = $this->handleNull("password", $this->user);
        $department = $this->handleNull("department", $this->user);
        $phoneNumber = $this->handleNull("phone_number", $this->user);
        $gender = $this->handleNull("gender", $this->user);

        if (empty($username)) {
            $this->fieldErrors['username'] = "This field is required...";
        } 

        // Validate Email 
        if (!empty($email)) {
            if (!$this->validateHelper->validateEmail($email)) $this->fieldErrors['email'] = "You entered an invalid email";
        } else $this->fieldErrors['email'] = "This field is required..";

        if (!empty($phoneNumber)) {
            if (!$this->validateHelper->validatePhoneNumber($this->handleNull("phone_number", $this->user))) $this->fieldErrors['phoneNumber'] = "You entered an invalid phone number";
        } else $this->fieldErrors['phoneNumber'] = "This field is required..";

        if (empty($department))  $this->fieldErrors['department'] = "Select department to assign to user..";

        if (!strlen((string)$gender)) {
            $this->fieldErrors['gender'] = "This field is required! $gender"; 
        }

        # Validate password
        if (!empty($password)) {
            if (!$this->validateHelper->checkPasswordStrength($this->handleNull("password", $this->user))) $this->fieldErrors['password'] = "Password Not Strong Enough, make sure it contains numbers, lowercase or uppercase, and special characters";
        } else $this->fieldErrors['password'] = "This field is required";
    }

    public function createUser(): ?bool
    {
        global $username, $email, $role, $department, $gender, $password, $phoneNumber;
        
        # Check if the username and email exist in database 
        if ($this->userModel->makeCountQueriesOfTable("username", $username)) $this->fieldErrors['username'] = "Username '$username' is not available, please use another";

        if ($this->userModel->makeCountQueriesOfTable("email", $email)) $this->fieldErrors['email'] = "Email '$email' is not available, please use another";

        $avatarName = "";

        if (AuthUserController::getAuthUser()['role_id'] == UserRole::STAFF_MEMBER && $this->user['role'] == UserRole::SYSTEM_ADMINISTRATOR || $this->user['role'] == UserRole::STAFF_MEMBER) $this->fieldErrors['role'] = "You have no privileges of registering a super admin yet you are an admin.";

        if (AuthUserController::getAuthUser()['role_id'] == UserRole::SYSTEM_ADMINISTRATOR && $this->user['role'] == UserRole::SYSTEM_ADMINISTRATOR) $this->fieldErrors['role'] = "You have no privileges of registering a super admin yet you are an admin.";

        if (!strlen($this->imageHelper->errors) and isset($_FILES['userAvatar']) and !count($this->fieldErrors)) {
            if (!empty($_FILES['userAvatar']['name'])) {
                $avatarName = $this->imageHelper->uploadImage($_FILES['userAvatar'], dirname(__DIR__) . "/images/users/");
                $avatarName = isset($avatarName) ? $avatarName : "";
            }
        }

        if (count($this->fieldErrors) or !empty($this->imageHelper->errors)) return null;

        # The Insert query
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $arrayData = ['username' => $username, 'email' => $email, 'password' => $hashedPassword, 'role_id' => $role, 'dept_id' => $department, 'phone_number' => $phoneNumber, 'uniqid' => $this->generateUniqid(), 'avatar' => $avatarName, 'gender' => $gender];

        $this->userModel->createUser($arrayData);

        return true;
    }

    public function deleteUser(int $delId, string $userAvatar)
    {
        if ($this->userModel->deleteItemFromTable("id", $delId)) {
            $this->imageHelper->removeImage(dirname(__DIR__) . "/images/users/", $userAvatar);
            return true;
        }
        return false;
    }

    public function verifyPasswordAlgorithm(string $passwordHash): int
    {
        return password_get_info($passwordHash)['algo'] == PASSWORD_BCRYPT;
    }

    public function hashPassword(string $password)
    {
        return password_hash($password, $this->userModel::$passwordHashingAlgorithm);
    }

    /**
     * Updates password by email 
     * 
     * @param string $email 
     * @param string password 
     */
    public function updatePasswordByEmail(string $email, string $password)
    {
        $this->userModel->preparedUpdate(set: ["password" => $this->hashPassword($password)], where: ["email" => $email]);

        return True; 
    }
}
