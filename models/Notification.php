<?php 

namespace App\models; 

use App\core\Model;
use App\interfaces\ModelInterface;

class Notification extends Model implements ModelInterface 
{
    public function __construct(
        public string $tableName = "notifications"
    )
    {}

    public function getAll() {
        return $this->fetchAllData();
    }

    public function create(array $notificationData): bool
    {
        $results = $this->doInsert($notificationData);
        return $results; 
    }

    public function update(array $setClause, array $whereClause): bool
    {
        $results = $this->preparedUpdate($setClause, $whereClause); 
        return $results; 
    } 

    public function get(string $notificationId): array|bool
    {
        $notification = $this->getRecordsBy("id", $notificationId);
        return $notification; 
    }

    public function delete(int $notificationId): bool 
    {
        $results = $this->preparedDelete(["id" => $notificationId]);
        return $results;
    }
}