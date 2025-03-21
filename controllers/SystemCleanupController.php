<?php

namespace App\controllers;

use App\core\{Controller, Model};


class SystemCleanupController extends Controller
{
    public \PDO $usePDO;

    public array $checkupReport = [];

    public function __construct(public readonly string $imageDirPath, private int $mode = 0)
    {
        $this->usePDO = Model::staticUsePDO();
    }

    public function runCheckup()
    {
        $this->cleanupUserImageRecordsInDatabase();
        $this->cleanupStaffDataImageRecordsInDB();
        $this->removeDanglingStaffDataRecords();

        return $this->checkupReport;
    }

    public function runCleanup()
    {
        $this->mode = 1;

        $this->cleanupUserImageRecordsInDatabase();
        $this->cleanupStaffDataImageRecordsInDB();
        $this->removeDanglingStaffDataRecords();

        return true;
    }

    public function getImagesFromImageDir($dirName): array
    {
        $images = scandir("$this->imageDirPath/$dirName");
        unset($images[0]);
        unset($images[1]);

        return $images;
    }

    public function fetchDataFromTable($tableName, $columns = ["*"])
    {
        $columns = implode(", ", $columns);
        $query = $this->usePDO->query("SELECT $columns FROM $tableName");

        return $query->fetchAll();
    }


    /**
     * Runs and executes SQL Statements 
     *
     * @param string $sql
     * @return array|bool|null
     */
    public function runQuery(string $sql, bool $hasReturnValue = true, bool $multiple = false)
    {
        if (!strlen($sql)) {
            return null;
        }

        $stmt = $this->usePDO->query($sql);
        if ($hasReturnValue) {
            $results = $multiple ? $stmt->fetchAll() : $stmt->fetch();
            return $results;
        }
        return true;
    }


    public function cleanupUserImageRecordsInDatabase(): void
    {
        $usersImagesFromUsersFolder = $this->getImagesFromImageDir("users");
        $imagesFromDB = $this->fetchDataFromTable("users", ["id", "avatar"]);

        # To get the deleted and unreferenced images count
        $danglingImagesCount = 0;

        $danglingImageRecordIds = [];

        foreach ($imagesFromDB as $userImage) {
            if (!in_array($userImage["avatar"], $usersImagesFromUsersFolder) && !empty($userImage["avatar"]))
                $danglingImageRecordIds[] = $userImage["id"];
        }

        $danglingImagesCount += count($danglingImageRecordIds);

        if ($this->mode == 1) {
            # Removing the avatars with no images 
            foreach ($danglingImageRecordIds as $userRecordId) {
                $this->runQuery("UPDATE users SET avatar = NULL where id = '$userRecordId'", hasReturnValue: false);
            }
        }


        # Empty $danglingImages after use
        $danglingImages = [];

        # Getting Only Images
        $imagesFromDB = array_map(fn($item) => $item["avatar"], $imagesFromDB);

        # For images in the staff_images directory 
        foreach ($usersImagesFromUsersFolder as $dirImage) {
            if (!in_array($dirImage, $imagesFromDB))
                $danglingImages[] = $dirImage;
        }

        if ($this->mode == 1) {
            # Deleting the images that are not referenced by any record in the db
            foreach ($danglingImages as $image) {
                @unlink("$this->imageDirPath/users/$image");
            }
        }

        # Getting the total number of dangling images in the users table
        $this->checkupReport["dangling_images_for_users"] = $danglingImagesCount + count($danglingImages);
    }

    public function cleanupStaffDataImageRecordsInDB()
    {
        $imagesFromDB = $this->fetchDataFromTable("staff_data_images", ["id", "name"]);
        $imagesFromStaffImagesFolder = $this->getImagesFromImageDir("staff_images");

        # To get the deleted and unreferenced images count
        $danglingImagesCount = 0;

        $danglingImages = [];

        foreach ($imagesFromDB as $imageFromDB) {
            if (!in_array($imageFromDB["name"], $imagesFromStaffImagesFolder)) $danglingImages[] = $imageFromDB["id"];
        }

        $danglingImagesCount += count($danglingImages);

        if ($this->mode == 1) {
            # Delete the images that are not linked 
            foreach ($danglingImages as $imageRecordId) {
                $this->runQuery("DELETE FROM staff_data where id = '$imageRecordId'", hasReturnValue: false);
            }
        }

        # Empty $redundantImages after use
        $danglingImages = [];

        # Getting Only Images
        $imagesFromDB = array_map(fn($item) => $item["name"], $imagesFromDB);

        # For images in the staff_images directory 
        foreach ($imagesFromStaffImagesFolder as $dirImage) {
            if (!in_array($dirImage, $imagesFromDB))
                $danglingImages[] = $dirImage;
        }

        if ($this->mode == 1) {
            # Deleting the images that are not referenced by any record in the db
            foreach ($danglingImages as $image) {
                @unlink("$this->imageDirPath/staff_images/$image");
            }
        }

        # Getting the total number of dangling images in the staff_data table
        $this->checkupReport["dangling_images_for_staff_data_count"] = count($danglingImages) + $danglingImagesCount;
    }

    public function removeDanglingStaffDataRecords()
    {
        $allStaffData = $this->fetchDataFromTable("staff_data", ["id", "user_id"]);

        $danglingStaffRecords = [];

        # Get all redundant staff records
        foreach ($allStaffData as $staffRecord) {
            if (is_null($staffRecord["user_id"])) {
                $danglingStaffRecords[] = $staffRecord["id"];
            }
        }

        if ($this->mode == 1) {
            # Delete all staff data records
            foreach ($danglingStaffRecords as $staffId) {
                $this->runQuery("DELETE FROM staff_data WHERE id = $staffId");
            }
        }

        $this->checkupReport["dangling_staff_records_count"] = count($danglingStaffRecords);
    }
}
