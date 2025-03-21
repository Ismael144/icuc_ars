<?php 

namespace App\models; 

use App\core\Model;

class StaffDataChange extends Model 
{
    public string $tableName = "staff_data_changes"; 

    public function __construct() 
    {
        
    }

    /**
     * A change is removed after is it viewed by the system
     * 
     * @param int $changeId
     * @return bool
     */
    public function removeChange(int $changeId) 
    {   
        $where = ["id" => $changeId];
        $this->runQuery("DELETE FROM {$this->tableName} WHERE id = '$changeId'");
        return true; 
    }
}