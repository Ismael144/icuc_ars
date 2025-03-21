<?php 

use App\Database\Database;
use App\Database\DBConfigurationEnum;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    public function testDatabaseMock() 
    {
        $dbMock = $this->createMock(Database::class); 
    }
}