<?php

namespace App\controllers;

use App\core\Model;
use App\models\User;
use App\core\Controller;
use App\enums\UserStatus;
use App\helpers\{SessionHelper, ValidationHelper, ImageHelper};

class AuthUserController extends Controller
{
    public ImageHelper $imageHelper;

    public static SessionHelper $sessionHelper;

    public static ValidationHelper $validateHelper;

    public UserActivitiesController $userActivitiesController;

    public array $fieldErrors;

    public array $user = [];

    public string $imageDestination = "/icuc_ars/images/users/";

    public string $imagePath = "";

    public static array $authUser = [];

    public User $userModel;

    public function __construct()
    {
        $this->userModel = new User;
        $this->fieldErrors = [];
        $this->imagePath = \dirname(__DIR__) . "/images/staff_images/";
        self::$sessionHelper = new SessionHelper;
        $this->imageHelper = new ImageHelper($this->imageDestination);
        self::$validateHelper = new ValidationHelper;

        if ($this->requestMethod('post'))
            $this->user = [
                "username"         => self::$validateHelper->filter($this->handleNull('username', $_POST)),
                "email"            => self::$validateHelper->filter($this->handleNull('email', $_POST)),
                "password"         => self::$validateHelper->filter($this->handleNull('password', $_POST)),
                "role"             => self::$validateHelper->filter($this->handleNull('role', $_POST)),
                "phone_number"     => self::$validateHelper->filter($this->handleNull('phone_number', $_POST)),
                // Sometimes the avatar field is string, doing typing cast to it
                "avatar"           => $this->handleNull('avatar', $_FILES),
                "verificationCode" => self::$validateHelper->filter($this->handleNull('verificationCode', $_POST)),
            ];
    }

    /**
     * Signs user in creating a username and uniqid session
     *
     * @return array<mixed>|bool 
     */
    public function authenticate(): array|bool
    {
        if (!$this->userModel->checkPasswordIfValid($this->user['email'], $this->user['password'])) {
            return false;
        }

        $fetchedUser = $this->userModel->preparedSelect(['id', 'role_id', 'uniqid'], ['email' => $this->user['email']]);

        return $fetchedUser;
    }

    /**
     * Signs user in creating a username and uniqid session
     *
     * @return boolean
     */
    // #[RecordUserActivity(AttributeType, Message)]
    public function signIn(): bool
    {
        if ($this->authenticate()) {
            # Checks whether the user is available in database
            self::$authUser = $this->authenticate();

            # Setting the sessions
            self::$authUser["role"] = $this->userModel->getRoleById(self::$authUser["role_id"]);
            self::$sessionHelper->set(is_authenticated: true);
            self::$sessionHelper->set(user_id: self::$authUser["id"]);
            self::$sessionHelper->set(user_uniqid: self::$authUser["uniqid"]);

            # Update last login
            $this->updateLastLogin();

            # Updating the status 
            $this->updateStatus(UserStatus::ACTIVE);

            return true;
        } else {
            $this->setFieldError("signinError", "Wrong username password combination");
            return false;
        }
    }

    private function updateLastLogin()
    {
        $authUser = self::getAuthUser();
        $this->userModel->updateLastLogin($authUser["id"]);
    }


    private function updateStatus(UserStatus $status)
    {
        $authUser = self::getAuthUser();
        $this->userModel->updateStatus($status, $authUser["uniqid"]);
    }

    public function __get($name)
    {
        if ($name == "authUser") {
            return self::getAuthUser();
        }
    }

    /**
     * Gets auth user 
     *
     * @return ?array
     */
    public static function getAuthUser(): ?array
    {
        $sessionHelper = new SessionHelper;
        # Some validation here
        if (
            $sessionHelper->get('user_uniqid') == null ||
            $sessionHelper->get('is_authenticated') == null ||
            $sessionHelper->get('user_id') == null
        ) return null;

        # Getting user uniqid and id
        $userId = $sessionHelper->get('user_id');
        $userUniqid = $sessionHelper->get('user_uniqid');

        # Making a traditional query
        $stmt = Model::staticUsePDO()->prepare("
            SELECT * FROM 
            " . User::$staticTableName . " 
            WHERE id = ? AND uniqid = ?
        ");

        $stmt->execute([$userId, $userUniqid]);

        # Checking whether the user exists, just in case 
        # Returns null if does not exist
        if (!$stmt->rowCount()) return null;

        $authUser = $stmt->fetch();

        # Get role of the user
        $roleFetchQuery = Model::staticUsePDO()->query("SELECT * FROM " . User::$rolesTableName . " WHERE id = {$authUser['role_id']}");

        # Get role and assign it to authUser role_id
        $authUser["role"] = $roleFetchQuery->fetch()["role"];

        # Get department of the user
        $roleFetchQuery = Model::staticUsePDO()->query("SELECT * FROM " . User::$departmentsTableName . " WHERE id = {$authUser['dept_id']}");

        # Get department and assign it to authUser dept_id
        $authUser["dept"] = $roleFetchQuery->fetch()["name"];

        return $authUser;
    }

    public function signInValidations()
    {
        $email = $this->handleNull('email', $this->user);
        $password = $this->handleNull('password', $this->user);

        if (self::$validateHelper->isEmptyField($this->handleNull('email', $this->user))) $this->setFieldError("email", "An email is required inorder to continue");

        if (self::$validateHelper->isEmptyField($this->handleNull('password', $this->user))) $this->setFieldError("password", "A password is required inorder to continue");
    }

    /**
     * Checks if a user is authenticated | signed in
     *
     * @return boolean
     */
    public static function isAuthenticated(): bool
    {
        return !is_null(self::$sessionHelper->get('is_authenticated'));
    }

    /**
     * Signs the user out, by destroying all sessions 
     *
     * @return bool
     */
    public function signOut(): bool
    {
        self::$sessionHelper->destroyAll();
        return true;
    }

    /**
     * Will make sure the role of the user is valid
     *
     * @return ?bool 
     */
    public function update(): ?bool
    {
        # Username Existence Checkups
        $usernameResults = $this->userModel->preparedSelect(["username"], ["username" => $this->user['username']]);

        if ($this->user["username"] != self::getAuthUser()["username"] && count($usernameResults)) {
            $this->fieldErrors['username'] = "Username '{$this->user['username']}' is not available, please use another";
        }

        # Email Existence Checkups 
        $emailResults = $this->userModel->preparedSelect(["email"], ["email" => $this->user['email']]);

        if ($this->user["email"] != self::getAuthUser()["email"] && count($emailResults)) {
            $this->fieldErrors['email'] = "Email Address '{$this->user['email']}' is not available, please use another";
        }

        $userAvatar = strlen($this->user['avatar']['name']) ? $this->user['avatar'] : "";

        $fileName = $this->authUser['avatar'];

        if (is_array($userAvatar)) {
            # if has old image, it will be deleted and wont cause error if image doesnt exist
            $this->imageHelper->removeImage(dirname(__DIR__) . "/images/users/", $this->authUser['avatar']);

            # Either returns file name when upload is successful or boolean
            $fileName = $this->imageHelper->uploadImage($this->user["avatar"], dirname(__DIR__) . "/images/users/", $this->authUser['avatar']);

            if (strlen($this->imageHelper->errors)) $this->fieldErrors["avatar"] = $this->imageHelper->errors;
        }

        if (count($this->fieldErrors)) return null;

        $stmt = $this->userModel->usePDO()->prepare("UPDATE {$this->userModel->tableName} SET username = :username, email = :email, phone_number = :phone_number, avatar = :avatar WHERE id = :userAuthId");
        $stmt->execute(["username" => $this->user["username"], "email" => $this->user["email"], "phone_number" => $this->user["phone_number"], "avatar" => $fileName, "userAuthId" => self::getAuthUser()["id"]]);

        return true;
    }

    public function hashPassword(string $password)
    {
        return password_hash($password, $this->userModel::$passwordHashingAlgorithm);
    }

    public function updatePassword(string $currentPassword, string $newPassword): bool
    {
        // Check whether the current user password matches the one entered in 
        $currentlyHashedPassword = $this->userModel->preparedSelect(["password"], ["uniqid" => self::getAuthUser()["uniqid"]]);

        if (password_verify($currentPassword, $currentlyHashedPassword["password"])) {
            $newPassword = $this->hashPassword($newPassword);
            // Updating the password here 
            $this->userModel->preparedUpdate(["password" => $newPassword], ["uniqid" => self::getAuthUser()["uniqid"]]);

            return true;
        }

        return false;
    }

    public function formValidation()
    {
        $username = $this->handleNull("username", $this->user);
        $email = $this->handleNull("email", $this->user);
        $password = $this->handleNull("password", $this->user);
        $role = $this->handleNull("role", $this->user);
        $phoneNumber = $this->handleNull("phone_number", $this->user);

        if (!empty($username)) {
            if (!self::$validateHelper->isUsernameValid($username)) $this->fieldErrors['username'] = "Your username is invalid, make sure it does not contain characters like '$@#$%^'";
        } else $this->fieldErrors['username'] = "This field is required...";

        // Validate Email 
        if (!empty($email)) {
            if (!self::$validateHelper->validateEmail($email)) $this->fieldErrors['email'] = "You entered an invalid email";
        } else $this->fieldErrors['email'] = "This field is required..";

        if (!empty($phoneNumber)) {
            if (!self::$validateHelper->validatePhoneNumber($this->handleNull("phone_number", $this->user))) $this->fieldErrors['phoneNumber'] = "You entered an invalid phone number";
        } else $this->fieldErrors['phoneNumber'] = "This field is required..";

        # Validate password
        if (!empty($password)) {
            if (!self::$validateHelper->checkPasswordStrength($this->handleNull("password", $this->user))) $this->fieldErrors['password'] = "Password Not Strong Enough, make sure it contains numbers, lowercase or uppercase, and special characters";
        } else $this->fieldErrors['password'] = "This field is required";
    }

    public function verifyPasswordAlgorithm(string $passwordHash): int
    {
        return password_get_info($passwordHash)['algo'] == PASSWORD_BCRYPT;
    }

    /**
     * Displays an alert whenever the users signs in
     *
     * @return void
     */
    public function loginAlert()
    {
        if (!isset($_SESSION['loginSession'])) return;

        $authUser = self::getAuthUser();
        $authUsername = $authUser["username"];
        $message = $this->handleNull('last_login', $authUser) == null ? "Hi <b>{$authUsername}</b>, Welcome at the ICUC Attendance Monitoring System." : "Welcome back <b>{$authUsername}</b>, Its nice to see you again.";

        $loginHTML = <<<"HTML"
            <script>
                new swal({
                    title: "Sign In",
                    html: "<span style='font-size: 16px !important;'>{$message}•(●'◡'●)•</span>",
                    icon: "success",
                    button: "Ok",
                    confirmButtonClass: "btn btn-success w-xs me-2 mt-2",
                    timer: 2e3,
                })
            </script>
        HTML;

        if (self::$sessionHelper->get('loginSession') != null) {
            echo $loginHTML;
            self::$sessionHelper->unset('loginSession');
            $this->updateLastLogin();
        }
    }

    public function makeVerificationCode(): int
    {
        return rand(100000, 999999);
    }

    
    public function emailSearch(string $email): bool
    {
        $stmt = $this->userModel->usePDO()->prepare("SELECT email FROM {$this->userModel->tableName} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() ? true : false;
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
    }
}
