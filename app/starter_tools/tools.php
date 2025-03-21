<?php

use App\enums\UserRole;
use App\enums\SourceLocationPaths;
use App\controllers\UserController;
use App\controllers\AuthUserController;
use App\controllers\UserDepartmentController;
use App\attributes\managers\UserActivitiesManager;
use App\controllers\{StaffDataController, WebController};

# Composer Autoload File Brought In  
require dirname(__DIR__) . "/../vendor/autoload.php";

# *********************************

# Start the session if its not yet started
if (session_name() == "") session_start();

# Change timezone incase its not the write one
date_default_timezone_set('Africa/Kampala');

# To set the project path name e.g. app folder
$appDirPath = strlen(SourceLocationPaths::BASE_PROJECT_DIR_NAME->value) ? "/" . SourceLocationPaths::BASE_PROJECT_DIR_NAME->value . "/app/" : "";

# Initializing the Controllers 
$webController = new WebController;
$authUserController = new AuthUserController;
$userDeptController = new UserDepartmentController;

# Setting the auth user
$authUser = $authUserController::getAuthUser();

if (isset($authUser['dept_id'])) {
    $department = count($userDeptController->userDeptModel->getRecordsBy('id', $authUser['dept_id'])) ? $userDeptController->userDeptModel->getRecordsBy('id', $authUser['dept_id'])['name'] : ''; 
    $authUser['department'] = $department;
}

# Checking if logged in user is an admin
function isAdmin()
{
    return (new UserController)->userModel->allowByRoles(UserRole::SYSTEM_ADMINISTRATOR);
}

# Check Request method
function requestMethod(string $method)
{
    return strtolower($_SERVER['REQUEST_METHOD']) == strtolower($method);
}


# Handles Null producing logic
function handleNull(string|int $key, array $array)
{
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }

    return false;
}

/**
 * Redirect to a given page if a user is not authenticated
 *
 * @param string $redirectURL
 * @return void
 */
function authProtect(string $redirectURL)
{
    global $authUserController;
    if (!(AuthUserController::isAuthenticated())) {
        $authUserController::$sessionHelper->set(_usersession__notice: "User Sign In: Please first sign in before proceeding...");
        $authUserController->redirect($redirectURL);
    }
}

/**
 * Redirects users that are not system admins
 *
 * @param string $url
 * @param string $message
 * @return void
 */
function adminAuthProtect(string $url, string $message = "You have no priviledges to access that page.")
{
    global $webController;
    if (!isAdmin()) {
        $webController->sessionHelper->set(_user_session__notice: $message);
        $webController->redirect($url);
    }
}

/**
 * Works like $_GET super global 
 *
 * @param [type] $key
 * @return mixed
 */
function get($key): mixed
{
    return isset($_GET[$key]) ? $_GET[$key] : null;
}

/**
 * Works like $_POST super global 
 *
 * @param [type] $key
 * @return mixed
 */
function post($key): mixed
{
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

$userActivitiesManager = new UserActivitiesManager;
$userActivitiesManager->registerAttributesFromClasses([StaffDataController::class]);

/**
 * Helps in putting pagination navigation controls
 *
 * @param array $paginationArray this is got from the Model::paginateData method
 * @return void
 */
function paginationNavigation(array $paginationArray)
{
    [
        "current_page" => $currentPage,
        "previous_page" => $previousPage,
        "next_page" => $nextPage,
        "total_pages" => $totalPages
    ] = $paginationArray;

    echo <<<"HTML"
        <ul class="pagination my-2 mt-3">
    HTML;

    if ($currentPage > 1) {
        echo <<<"HTML"
            <li class="page-item"><a class="page-link" href="?page={$previousPage}"><i class="fas fa-chevron-left"></i></a></li>
        HTML;
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $paginationClass = $currentPage == $i ? "active" : "";
        echo <<<"HTML"
            <li class="page-item {$paginationClass} mx-1">
                <a class="page-link" href="?page={$i}">$i</a>
            </li>
        HTML;
    }

    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        echo <<<"HTML"
            <li class="page-item"><a class="page-link" href="?page=$nextPage"> <i class="fas fa-chevron-right"></i> </a></li>
        HTML;
    }

    echo <<<"HTML"
        </ul>
    HTML;

    echo "Viewing Page $currentPage of $totalPages";
}

function alert(...$keyValueArgs): mixed
{
    foreach ($keyValueArgs as $sessionKey => $value) {
        $_SESSION[$sessionKey] = $value;
        return $value;
    }

    return null;
}

/**
 * Redirects to desired page and also sets a session
 * 
 * @param string $url this is the url to direct to
 * @param array $sessionData Contains key => value 
 * 
 * Read the documentation of the method called `handleAllAlertSessions` in the `WebController class`
*/
function session_redirect(string $url, array $sessionData) {
    global $webController; 

    foreach($sessionData as $key => $content) {
        $_SESSION[$key] = $content; 
    }

    $webController->redirect($url);
}

function userImage(?string $image) {
    global $appDirPath;
    $imgPath = !strlen($image) ? "{$appDirPath}assets/images/user-avatar.png" : "/icuc_ars/images/users/{$image}";
    
    return $imgPath;
}
