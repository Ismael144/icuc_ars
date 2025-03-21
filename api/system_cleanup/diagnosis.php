<?php

require_once dirname(__DIR__) . "/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\SystemCleanupController;

$appAPIHandler = new WebAPIHandler;
$systemCleanupController = new SystemCleanupController(dirname(__DIR__) . "/../images", 0);

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use ($systemCleanupController) {
    sleep(random_int(1.0, 2.4));
    $appAPIHandler->outputResponse($systemCleanupController->runCheckup());
});
