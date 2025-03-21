<?php

namespace App\faceDetector;

use Amp\Future;
use Amp\Parallel\Worker;
use App\helpers\ImageHelper;
use App\controllers\StaffDataController;

class FaceDetectionController
{
    public ImageHelper $imageHelper;

    public StaffDataController $staffDataController; 

    public FaceDetectionImagesController $faceDetectionImagesController;

    // This is the URL to a custom API I designed just for face detection.
    public string $faceDetectionAPIURL = "https://icuc-arms-api.onrender.com/detect_face";

    public function __construct(private array $uploadedImages = [], private int $userId)
    {
        $this->userId = $userId; 
        $this->imageHelper = new ImageHelper;
        $this->uploadedImages = $uploadedImages;
        $this->staffDataController = new StaffDataController; 
        $this->faceDetectionImagesController = new FaceDetectionImagesController($uploadedImages);
    }

    /**
     * Detects faces in the given images using the specified face detection API, by using AMPHP's powerful parallel processing library.
     *
     * @return array The responses from the face detection API.
     */
    public function detect(): array
    {
        // Format the images into a more easy way of accessing each image's metadata, read the method's documentation 
        $images = $this->faceDetectionImagesController->format();
        
        $executions = [];

        // Checking if a user image upload limit is reached
        if (count((new StaffDataController)->staffDataImagesModel->getImagesBy($this->userId, 'data_id')) >= $this->imageHelper::IMAGE_UPLOAD_LIMIT) {
            return ['identifier' => [
               'error' => "Sorry, you can only upload ". $this->imageHelper::IMAGE_UPLOAD_LIMIT ." images per user"
            ]];
        }

        // Getting staff record by user's id
        $staffDataRecord = $this->staffDataController->staffDataModel->getRecordsBy('user_id', $this->userId, multiple: false); 

        // Creating a separate task for each of the images created and then appending them to an executions array
        foreach ($images as $_ => $image) {
            $executions[$image['name']] = Worker\submit(new FaceDetectionControllerTask($staffDataRecord["id"], $image, $this->faceDetectionAPIURL));
        }

        // Responses for each of the images 
        $responses = Future\await(array_map(
            fn (Worker\Execution $execution) => $execution->getFuture(),
            $executions
        ));

        $img_path = dirname(__DIR__)."/images/staff_images/";
        
        // Dealing with the response e.g. (when a face wasn't detected in image)
        foreach($responses as $imageResponse) {
            // If level is set, then it means that the error is critical, therefore aborting the entire operation
            if (isset($imageResponse['level'])) break; 

            // If success is not set, then it skips because an error occured 
            if (!isset($imageResponse["success"])) continue; 
            
            // If successful, upload and save the images to the staff_images directory 
            if ($imageResponse["success"] == true) {
                $this->faceDetectionImagesController->uploadAndSave($imageResponse["image"], $img_path, $staffDataRecord["id"]);
            }
        }

        return $responses; 
    }
}