<?php

namespace App\controllers;

use App\core\Controller;
use App\models\Attendance;
use App\helpers\ValidationHelper;
use App\models\AttendanceHoliday;
use App\helpers\RequestDataHelper;
use App\interfaces\ModelControllerInterface;

class AttendanceHolidaysController extends Controller implements ModelControllerInterface
{
    public array $holidayForm = [];
    
    public ValidationHelper $validateHelper;
    
    public AttendanceHoliday $attendanceHolidayModel; 

    public Attendance $attendanceDataModel; 

    public function __construct()
    {
        $this->attendanceDataModel = new Attendance;
        $this->validateHelper = new ValidationHelper;
        $this->attendanceHolidayModel = new AttendanceHoliday;

        if (RequestDataHelper::method('POST')) {
            $this->holidayForm = [
                "name" => $this->validateHelper->filter(handleNull("name", $_POST)),
                "date" => $this->validateHelper->filter(handleNull("date", $_POST)),
                "description" => $this->validateHelper->filter(handleNull("description", $_POST)),
                "is_recursive" => $this->validateHelper->filter(handleNull("is_recursive", $_POST)),
            ];
        }
    }

    // 'getTableData', 'getTableRecordsCount', 'getTableName'

    public function getTableData(): array
    {
        return $this->attendanceHolidayModel->fetchAllData();
    }

    public function getTableRecordsCount(): int|bool
    {
        return $this->attendanceHolidayModel->getTableRecordsCount();
    }

    public function getTableName(): string
    {
        return $this->attendanceHolidayModel->tableName;
    }

    public function doValidations()
    {
        [
            "name" => $name,
            "date" => $date,
            "description" => $description,
            "is_recursive" => $recursive, 
        ] = $this->holidayForm;

        if (empty($name)) $this->setFieldError("name", "This field is required...");

        if (empty($date)) $this->setFieldError("date", "This field is required...");

        if (empty($recursive)) {
            $this->holidayForm['is_recursive'] = 1;
        } else {
            $this->holidayForm['is_recursive'] = 2;
        }

        if (empty($description)) {
            $this->setFieldError("description", "This field is required...");
        }

    }

    public function getHolidaysForCalendar()
    {
        $attendanceHolidayData = $this->attendanceHolidayModel->fetchColumns(['date', 'name']);
        return $attendanceHolidayData;
    }

    public function allHolidays()
    {
        $attendanceHolidayData = $this->attendanceHolidayModel->getAll();
        if (!is_null(get('search'))) $attendanceHolidayData = $this->attendanceHolidayModel->search(get('search'), ["name", "description", "date"]);
        return $attendanceHolidayData; 
    }

    public function save()
    {
        $holidayData = $this->holidayForm; 
        $this->attendanceHolidayModel->create($holidayData);
    }

    public function update(int $holidayId): array|bool 
    {
        $results =  $this->attendanceHolidayModel->edit($this->holidayForm, ["id" => $holidayId]);
        return $results;
    }

    public function deleteHoliday(int $id)
    {
        $results = $this->attendanceHolidayModel->delete(["id" => $id]);
        return $results; 
    }
}
