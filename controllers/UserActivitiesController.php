<?php 

namespace App\controllers;

use App\core\Controller;
use App\models\UserActitvity;

class UserActivitiesController extends Controller 
{ 
    public UserActitvity $userActivities; 

    public function __construct()
    {
        $this->userActivities = new UserActitvity();
    }

    public function fetchDateOccured()
    {
        $results = $this->userActivities->runQuery("SELECT DISTINCT datetime_occured FROM {$this->userActivities->tableName} WHERE user_id = {$this->userActivities->userId}", true, true);
        return $results;
    }

    public function fetchActivities(): ?array
    {
        return $this->userActivities->fetchActivities();
    }
}