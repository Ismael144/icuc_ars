<?php 

namespace App\interfaces; 

interface ModelControllerInterface 
{
    public function getTableData(): array; 
    public function getTableRecordsCount(): int|bool;
    public function getTableName(): string; 
}