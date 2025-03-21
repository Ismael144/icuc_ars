<?php 

namespace App\models; 

use App\core\Model;

class AttendanceHoliday extends Model
{
    public string $tableName = "attendance_holidays";

    public function getAll()
    {
        $data = $this->fetchAllData();
        return $data; 
    }

    public function create($data): bool
    {
        $results = $this->doInsert($data);
        return $results; 
    }

    public function edit($setClause, $whereClause): bool 
    {
        $results = $this->preparedUpdate($setClause, $whereClause);
        return $results;
    }

    public function get(int $id): array | bool
    {
        $results = $this->getRecordsBy("id", $id, false);
        return $results;
    }

    public function delete(array $whereClause): bool 
    {
        $results = $this->preparedDelete($whereClause); 
        return $results;
    }
}