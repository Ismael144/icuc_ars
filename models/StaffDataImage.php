<?php

namespace App\models;

use App\core\Model;

use App\interfaces\ModelInterface;

class StaffDataImage extends Model implements ModelInterface
{
    public function __construct(public string $tableName = "staff_data_images")
    {
    }

    public function getImagesBy(int $value, $by = "data_id")
    {
        $pdo = $this->usePDO();
        $stmt = $pdo->query("SELECT name FROM {$this->tableName} WHERE $by = $value");
        
        $data = $stmt->fetchAll();
        return $data;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function deleteRecordFromTable(int|string $by, mixed $value) {
        $pdo = $this->usePDO(); 
        $stmt = $pdo->prepare("DELETE FROM $this->tableName WHERE $by = ?"); 
        $stmt->execute([$value]);
        return true; 
    }

    public function insertImages(array $data)
    {
        $pdo = $this->usePDO();
        ["name" => $imgNames, "dataId" => $dataId] = $data;

        $stmt = $pdo->prepare("INSERT INTO $this->tableName(data_id, name) VALUES(:dataId, :name)");
        $stmt->execute(['dataId' => $dataId, "name" => $imgNames]);
    }
}
