<?php 

use App\core\Model;

class ModelTest extends \PHPUnit\Framework\TestCase
{
    private Model $model; 

    public function setUp(): void
    {
        $this->model = new Model;
    }

    public function testFetchDataFromTable()
    {
        $results = $this->model->fetchAllData("users");
        var_dump($results);
        $this->assertSame(gettype([]), gettype($results));
    }

    public function testGetDataCount()
    {
        $count = $this->model->getTableRecordsCount("users"); 
        var_dump($count); 
        exit();
    }
}