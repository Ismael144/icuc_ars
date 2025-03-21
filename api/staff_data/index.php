<?php 

require_once dirname(__DIR__)."/../vendor/autoload.php";

use App\core\WebAPIHandler;
use App\controllers\StaffDataController;

$appAPIHandler = new WebAPIHandler;
$staffDataController = new StaffDataController; 

$appAPIHandler->init(function (WebAPIHandler $appAPIHandler) use($staffDataController) {
    // Organize data 
    if ($appAPIHandler->getRequestData('id', 'get') != null) {
        $id = $appAPIHandler->getRequestData('id', 'get');
        $results = $staffDataController->getDataWithImagesForSingleById($id);
        
        if ($results == false) {
            $appAPIHandler->changeStatusCode(500); 
        } 

        $appAPIHandler->changeStatusCode(200);
        echo $appAPIHandler::jsonEncode($results['images'][0]); 

        return; 
    }


    $systemData = $staffDataController->getDataWithImages(); 
    $appAPIHandler->changeStatusCode(200);
    echo $appAPIHandler->jsonEncode($systemData);
});