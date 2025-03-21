<?php 

use App\Database\Database;
use App\Database\DBConfigurationEnum;
use App\GFS\GFServiceManager;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    public function testDatabaseMock() 
    {
        $dbMock = $this->createMock(GFServiceManager::class); 
    }
}