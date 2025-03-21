<?php

namespace App\faceDetector;

use App\controllers\StaffDataController;
use App\helpers\ImageHelper;
use Amp\{Parallel\Worker\Task, Cancellation, Sync\Channel};
use App\core\WebAPIHandler;

class FaceDetectionControllerTask implements Task
{
    public ImageHelper $imageHelper;

    public FaceDetectionImagesController $faceDetectionImagesController;

    public function __construct(
        private int $userId,
        private array $image,
        private readonly string $faceDetectionAPIURL,
    ) {
        $this->userId = $userId;
        $this->imageHelper = new ImageHelper;
        $this->faceDetectionImagesController = new FaceDetectionImagesController;
    }

    /**
     * Checks whether the uploaded image is valid
     * The validations are; 
     *  - An image should contain a face
     *  - An image should have only one face in it 
     * 
     * @param object $responseObj
     * @return string
     */
    private function isImageValid(object $responseObj): string
    {
        // Checking whether an image exists in image 
        if ($responseObj?->face_detected == true) {
            // Check whether the number of faces in an image dont exceed one(1)
            if ($responseObj?->numberOfFacesDetected > 1 || !$responseObj?->numberOfFacesDetected) {
                return "Expected 1 face, but found {$responseObj?->numberOfFacesDetected} Faces In the image";
            }

            // If they do, return an empty string 
            return "";
        } else {
            return "Make sure the image uploaded contains a face, Please make sure your image is clear...";
        }
    }

    /**
     * This method works as the worker initializer
     * 
     * @param \Amp\Sync\Channel $channel
     * @param \Amp\Cancellation $cancellation
     * @return array
     */
    public function run(Channel $channel, Cancellation $cancellation): array
    {
        try {
            $client = new \GuzzleHttp\Client();

            $imagePath = $this->image['tmp_name'];

            # Perform image validations
            $this->faceDetectionImagesController->validate($this->image);

            # Checking for errors, if exist, abort 
            if (isset($this->faceDetectionImagesController::$imageErrors[$this->image['name']])) {
                // Abort the operation
                $channel->close();
                // Return all the current image errors
                return [$this->faceDetectionImagesController::$imageErrors[$this->image['name']]];
            }

            # Check if user exceeds image limit, if yes, then abort the task
            if (count((new StaffDataController)->staffDataImagesModel->getImagesBy($this->userId, "data_id")) >= $this->imageHelper::IMAGE_UPLOAD_LIMIT) {
                $channel->close();
                $imageStatusResponse['success'] = false;
                $imageStatusResponse['errors'][] = "The maximum number of images you can upload are " . $this->imageHelper::IMAGE_UPLOAD_LIMIT;
                return ['error' => 'The image upload limit is only ' . $this->imageHelper::IMAGE_UPLOAD_LIMIT, 'level' => 'fatal'];
            }

            # Send the POST request with image data to the face detection api endpoint
            $response = $client->post($this->faceDetectionAPIURL, [
                'multipart' => [
                    [
                        'name' => 'image',  // The field name expected by the API
                        'contents' => fopen($imagePath, 'r'),
                        'filename' => basename($imagePath),
                    ],
                ]
            ]);

            # Json serializing the response
            $faceDetectionResponse = WebAPIHandler::jsonDecode($response->getBody()->getContents());

            # Handle the response
            $imageStatusResponse = [
                // Checking whether it has returned 1 or many errors
                "errors" => isset($this->faceDetectionImagesController::$imageErrors[$this->image['name']]) ? $this->faceDetectionImagesController::$imageErrors[$this->image['name']] : [],
                'success' => true,
                'image' => $this->image,
                'requestResults' => [
                    'status' => $response->getStatusCode(),
                    // If face is detected
                    'faceDetected' => $faceDetectionResponse?->face_detected,
                    // Number of faces detected in face
                    'numberOfFacesDetected' => $faceDetectionResponse?->numberOfFacesDetected,
                ]
            ];

            # Generate custom messages depending on response 
            if ($this->isImageValid($faceDetectionResponse)) {
                $imageStatusResponse['success'] = false;
                $imageStatusResponse['errors'][] = $this->isImageValid($faceDetectionResponse);
            }

            # if no errors exist, upload the image
            if (!count($imageStatusResponse['errors'])) {
                # Uploading the images 
                $imageStatusResponse["errors"] = isset($this->faceDetectionImagesController::$imageErrors[$this->image['name']]) ? $this->faceDetectionImagesController::$imageErrors[$this->image['name']] : [];
            } else {
                // If operation did not go successfully
                $imageStatusResponse['success'] = false;
            }

            return $imageStatusResponse;
        } catch (\Exception $e) {
            return ['criticalError' => $e->getMessage(), "image" => $this->image];
        }
    }
}
