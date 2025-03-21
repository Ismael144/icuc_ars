<?php

namespace App\models;

use App\core\Model;
use App\interfaces\ModelInterface;
use App\controllers\AuthUserController;

// Not implementated yet 

class UserActitvity extends Model implements ModelInterface
{
    public User $userModel;

    public ?int $userId;

    public string $currentDatetime;

    public function __construct(public string $tableName = "activities")
    {
        $this->userModel = new User;
        $this->currentDatetime = date('Y-m-d H:i:s');
        $this->userId = AuthUserController::getAuthUser()["id"];
    }

    public function fetchActivities()
    {
        $results = $this->runQuery("SELECT * FROM {$this->tableName} WHERE user_id = $this->userId", true, true);
        return $results;
    }

    public function createActivity(array $arrayData): ?bool
    {
        if (empty($arrayData)) return null;
        $arrayData["user_id"] = !isset($arrayData["user_id"]) || !$arrayData["user_id"] ? $this->userId : $arrayData["user_id"];
        return $this->doInsert($arrayData);
    }

    public function searchActivities(string $searchTerm): array
    {
        $stmt = $this->usePDO()->prepare("SELECT * FROM {$this->tableName} WHERE title LIKE :searchTerm OR body LIKE :searchTerm OR datetime_occured LIKE :searchTerm");
        $stmt->execute(['searchTerm' => $searchTerm]);
        $results = $stmt->fetchAll();
        return $results;
    }

    public function fetchLimitedActivities(int $limit = 7)
    {
        $results = $this->runQuery("SELECT * FROM {$this->tableName} ORDER BY datetime_occured DESC LIMIT $limit", true, true);
        return $results;
    }

    function getTimeElapsed($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diffString = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($diffString as $unit => $text) {
            if ($diff->$unit) {
                return $diff->$unit . ' ' . ($diff->$unit > 1 ? $text . 's' : $text) . ' ago';
            }
        }

        return $full ? $ago->format('Y-m-d H:i:s') : 'just now';
    }
}
