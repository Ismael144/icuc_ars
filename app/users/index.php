<?php
$pageTitle = "Users Index Page";
include_once dirname(__DIR__) . "/includes/header.php";

authProtect("../auth/signin");

?>

<?php

use App\controllers\{WebController, UserController};

$webController = new WebController;
$userController = new UserController;

adminAuthProtect("../dashboard");

$notAllowedTableRows = ["password", "avatar", "uniqid", "last_login"];

echo "<div id='layout-wrapper'>";
include dirname(__DIR__)."/includes/layouts/topbar.php";
include dirname(__DIR__)."/includes/layouts/sidebar.php";

$genderMapper = fn ($gender) => $userController->genderMapper($gender);

/* Renders the index page of a model */
$webController->RenderIndexPage($userController, configurations: [
    'keysToDrop' => $notAllowedTableRows,
    "date_created" => ['format_date', []],
    "gender" => $genderMapper,
    'username' => ['_capitalize'],
    'operations' => [
        'create' => isAdmin(),
        'delete' => isAdmin(),
        'edit'   => isAdmin(),
    ]
]);
echo "</div>";

include_once dirname(__DIR__) . "/includes/footer.php";

?>
