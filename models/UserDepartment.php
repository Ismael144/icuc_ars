<?php 

namespace App\models; 

use App\core\Model; 

class UserDepartment extends Model {
    public string $tableName = "user_departments"; 

    public function __construct() 
    {

    }

    public function create(array $createDeptData)
    {
        $results = $this->doInsert($createDeptData);
        return $results;
    }

    public function edit(array $editData): bool
    {
        $results = $this->preparedUpdate($editData, where: ["id" => $editData['id']]);
        return $results; 
    }

    public function getById(int|string $id): array|bool
    {
        $departmentResults = $this->getRecordsBy("id", $id); 
        return $departmentResults; 
    }

    public function delete(int $id): bool 
    {
        return $this->preparedDelete(["id" => $id]);
    }
}