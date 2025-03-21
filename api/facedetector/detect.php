<?php

use App\core\WebAPIHandler;
use App\faceDetector\{FaceDetectionController};

require dirname(__DIR__) . '/../vendor/autoload.php';

$faceDetectionAPIHandler = new WebAPIHandler;

$faceDetectionAPIHandler->init(actions: function (WebAPIHandler $APIHandler) {
    # Setting Request Headers
    if ($APIHandler->request->method != 'POST') {
        $response = $APIHandler->createArray(
            status: 405,
            error: "'{$APIHandler->request->method}' Method Not Allowed",
        );

        $APIHandler->outputResponse($response);

        $APIHandler->changeStatusCode(405);

        return;
    }

    $images = $APIHandler->getRequestData('images', 'files');
    if ($images == null || empty($images) || empty($images['name'][0])) {
        $response = $APIHandler::createArray(
            // status: $APIHandler->changeStatusCode(500),
            error: 'Upload some images to proceed',
        );

        $APIHandler->outputResponse($response);

        return;
    }

    if (count($images) > 10) {
        echo $APIHandler::jsonEncode(['identifier' => [
            // 'status' => $APIHandler->changeStatusCode(500),
            'error' => 'Wow, those are alot of images to process at once'
        ]]);

        return;
    }

    $userId = $APIHandler->getHeader('User-Id');

    if ($userId == null) {
        echo $APIHandler::jsonEncode(['identifier' => [
            'error' => 'A Data Id is required to continue.'
        ]]);

        return;
    }

    $faceDetectionController = new FaceDetectionController($images, $userId);
    $responses = $faceDetectionController->detect();
    $APIHandler->outputResponse($responses);
});
