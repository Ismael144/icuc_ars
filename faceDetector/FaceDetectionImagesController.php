<?php

namespace App\faceDetector;

use App\helpers\ImageHelper;
use App\models\StaffDataImage;

class FaceDetectionImagesController
{
    public ImageHelper $imageHelper;

    public StaffDataImage $sysImageModel; 

    public static array $imageErrors = [];

    public function __construct(
        public array $images = []
    ) {
        $this->images = $images;
        $this->sysImageModel = new StaffDataImage;
        $this->imageHelper = new ImageHelper;
    }


    /**
     * Format the uploaded images to a more readable format
     * From: `['name' => ['image1.png', 'image2.png']]`
     * To: `[['name' => 'image1.png'], ['name' => 'image2.png']]`
     * 
     * @return array<array>
     */
    public function format(): array
    {
        $imageArray = [];

        foreach ($this->images['name'] as $index => $name) {
            $image = [];
            foreach ($this->images as $key => $value) {
                $image[$key] = $value[$index];
            }
            $imageArray[] = $image;
        }

        return $imageArray;
    }

    /**
     * Validates images, for example like checking file size, 
     * for valid extensions etc
     *
     * @param array $image
     * @return void
     */
    public function validate(array $image): array | bool
    {
        if (!empty($image['name'])) {
            $size = $image['size'];
            $error = $image['error'];
            $file_name = $image['name'];
            $tmp_name = $image['tmp_name'];

            // Separating the file name and extension
            $tmpExtension = explode('.', $file_name);
            $file_ext = strtolower(end($tmpExtension));

            $newFileName = "ICUC-" . uniqid() . "-" . date("Y_m_d") . ".$file_ext";

            $new_destination = "./" . $newFileName;

            // Checking whether the uploaded file is an image
            if (in_array($file_ext, $this->imageHelper->getValidExtensions())) {
                if (!$error) {
                    if ($size < $this->imageHelper->getMaxImageSize()) {
                        move_uploaded_file($tmp_name, $new_destination);
                        
                        return [
                            'valid' => true,
                            'imageName' => $image['name'],
                            'newFileName' => $newFileName,
                            'tmpName' => $tmp_name
                        ];
                    } else {
                        self::$imageErrors[$image['name']][] = "The File You Uploaded Is Too Large";
                        return false;
                    }
                } else {
                    self::$imageErrors[$image['name']][] = "An Error Occured..";
                    return false;
                }
            } else {
                self::$imageErrors[$image['name']][] = "File of type '$file_ext' is not allowed";
                return false;
            }
        }
        return false;
    }

    /**
     * Uploads image to images folder and saves it into the database
     * 
     * @param array $image
     * @param string $path
     * @param int $dataId
     * @return string|bool 
     */
    public function uploadAndSave(array $image, string $path, int $dataId)
    {
        # Perform some validations before uploading them
        $fileName = $this->imageHelper->uploadImage($image, $path);
        if (!is_string($fileName)) return "Not String"; 
        
        $data = ["dataId" => $dataId, "name" => $fileName]; 
        $this->sysImageModel->insertImages($data);

        return true; 
    }
}