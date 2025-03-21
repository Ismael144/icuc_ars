<?php

namespace App\models;

use App\core\Model;

use App\interfaces\ModelInterface;

class AttendanceExcuse extends Model implements ModelInterface
{
    public \PDO $PDO;

    public function __construct(
        public string $tableName = "attendance_excuses"
    ) {
        $this->PDO = $this->usePDO();
    }

    /**
     * Returns the name of the table
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function makeExcuse(array $excuseData): bool
    {
        $this->doInsert($excuseData);
        return true; 
    }

    public function getSingleExcuse(int $excuseId): array|bool
    {   
        $excuseData = $this->getRecordsBy("id", $excuseId, false);
        return $excuseData; 
    }

    public function updateExcuse(array $updateData, array $whereClause): bool 
    {
        $results = $this->preparedUpdate($updateData, $whereClause);

        if ($results) {
            return true; 
        }
        return false;
    }
}