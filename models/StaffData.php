<?php 

namespace App\models; 

use App\core\Model;
use App\interfaces\ModelInterface;

class StaffData extends Model implements ModelInterface 
{
    public function __construct(
        public string $tableName = "staff_data"
    )
    {}

    public function update($sysData = [])
    {
        ["first_name" => $firstName, "last_name" => $lastName, "staff_user_id" => $staffId, "id" => $currentlyUpdated] = $sysData;
        $pdo = $this->usePDO();
        $stmt = $pdo->prepare("UPDATE {$this->tableName} SET first_name = :fName, last_name = :lName, user_id = :user_id WHERE id = :id");
        $stmt->execute(["fName" => $firstName, "lName" => $lastName, "user_id" => $staffId, "id" => $currentlyUpdated]);
    }

    /**
     * Only gets full name and staff id and to be sent to the AR System
     *
     * @return array
     */
    public function getPartialData(): array
    {
        return $this->runQuery("SELECT id, concat(first_name, ' ', last_name) as fullname FROM {$this->tableName}", multiple: true); 
    }
}