<?php
$pageTitle = "Holidays";
include dirname(__DIR__) . "/../includes/header.php"
    ?>

<?php

use App\controllers\AttendanceHolidaysController;

include dirname(__DIR__) . "/../includes/layouts/topbar.php";
include dirname(__DIR__) . "/../includes/layouts/sidebar.php";

$attendanceHolidaysController = new AttendanceHolidaysController;

$mapRecursive = fn ($value) => $value == "1" ? "True" : "False";

$webController->RenderIndexPage($attendanceHolidaysController, "Attendance Holidays", [
    "is_recursive" => $mapRecursive,
    "date" => ['format_date', []],
    "date_created" => ['format_date', []],
    'operations' => [
        'create' => isAdmin(),
        'delete' => isAdmin(),
        'edit' => isAdmin(),
    ]
]);

?>

<?php include dirname(__DIR__) . "/../includes/footer.php" ?>