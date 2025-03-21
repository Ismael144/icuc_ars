<?php 

use App\models\User;

class UserModelTest extends \PHPUnit\Framework\TestCase
{
    protected User $userModel; 
    
    public function setUp(): void 
    {
        $this->userModel = new User; 
    }

    public function testFetchDataFromTable()
    {
        $results = $this->userModel->fetchAllData($this->userModel->tableName);
        var_dump($results);
    }
}