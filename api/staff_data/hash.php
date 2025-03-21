<?php 

require_once dirname(__DIR__)."/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\StaffDataController;
use App\enums\Utilities; 

$appAPIHandler = new WebAPIHandler;
$staffDataController = new StaffDataController; 

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use($staffDataController) {
    // Returns the hash of staff data.
    $staffData = $staffDataController->getDataWithImages(); 
    $appAPIHandler->changeStatusCode(200);
    $key = Utilities::HASH_KEY->value;
    echo $appAPIHandler->jsonEncode(["hash" => hash_hmac('sha256', $appAPIHandler->jsonEncode($staffData), $key), "digest" => "sha256"]);
});