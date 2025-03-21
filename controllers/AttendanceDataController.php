<?php

namespace App\controllers;

use App\core\Controller;
use App\models\StaffData;
use App\models\StaffDataImage;
use App\models\{User, Attendance};
use App\interfaces\ModelControllerInterface;

class AttendanceDataController extends Controller implements ModelControllerInterface
{
    public User $userModel;

    public StaffData $staffDataModel;

    public Attendance $attendanceDataModel;

    public JSONSettingsController $jsonSettingsController;

    public function __construct()
    {
        $this->userModel = new User;
        $this->staffDataModel = new StaffData;
        $this->attendanceDataModel = new Attendance;
        $this->jsonSettingsController = new JSONSettingsController(dirname(__DIR__) . "/settings/attendance_settings.json");
    }

    public function getTableName(): string
    {
        return $this->attendanceDataModel->getTableName();
    }

    function getTableData(): array
    {
        $query = <<<"SQL"
            SELECT a.id as id, 
                a.staff_data_id as staff_id,
                concat(st.first_name, ' ', st.last_name) as full_name, 
                u.email,
                u.dept_id,
                u.phone_number,
                a.arrival_time,
                a.departure_time,
                a.date_attended
            FROM attendance AS a
                JOIN {$this->staffDataModel->tableName} AS st
                ON a.staff_data_id = st.id
                JOIN users AS u
                ON st.user_id = u.id
            ORDER BY a.staff_data_id, st.id;
        SQL;
        $results = $this->attendanceDataModel->runQuery($query, true, true);
        return $results;
    }

    public function getTableRecordsCount(): bool|int
    {
        return $this->attendanceDataModel->getTableRecordsCount();
    }

    public function getAttendanceSettings(): array
    {
        $attendanceSettings = $this->jsonSettingsController->readSettings()['attendanceTime'];
        return $attendanceSettings;
    }

    public function interpreteTimeLate(array $elapsed)
    {
        if ($elapsed['hours'] == 0 && $elapsed['minutes'] < 30) {
            return "Arrived In Time";
        } else {
            if ($elapsed['hours'] > 0) {
                if ($elapsed['minutes'] > 0) {
                    return "{$elapsed['hours']} hrs and {$elapsed['minutes']} mins late";
                } else {
                    return "{$elapsed['hours']} hrs late";
                }
            } else {
                return "{$elapsed['minutes']} mins late";
            }
        }
    }


    public function isCheckinOrCheckoutTime(): array
    {
        $attendanceSettings = $this->getAttendanceSettings();
        $checkInTime = $attendanceSettings['arrival_time'];
        $checkOutTime = $attendanceSettings['departure_time'];

        $currentTime = date('H:i:s'); // Get current time in HH:MM:SS format

        // Convert times to timestamps for easier comparison
        $checkInTimestamp = strtotime($checkInTime);
        $checkOutTimestamp = strtotime($checkOutTime);
        $currentTimeStamp = strtotime($currentTime);

        $checkResults = [];

        if ($currentTimeStamp >= $checkInTimestamp && $currentTimeStamp <= $checkOutTimestamp) {
            $checkResults["check_in"] = true;
            $checkResults["check_out"] = false;
        } else if ($currentTimeStamp >= $checkOutTimestamp) {
            $checkResults["check_in"] = false;
            $checkResults["check_out"] = true;
        } else {
            $checkResults["check_in"] = false;
            $checkResults["check_out"] = false;
        }

        return $checkResults;
    }


    public function getDateAttended(): array
    {
        return $this->attendanceDataModel->runQuery("SELECT DISTINCT date_attended FROM {$this->attendanceDataModel->tableName}", multiple: true);
    }

    public function getWhereDateAttended(string $date): array
    {
        $dateData = [];

        foreach ($this->getTableData() as $record) {
            if ($record["date_attended"] == $date) $dateData[] = $record;
        }

        return $dateData;
    }

    public function getRecordsBy(string $what, string $where, string $value): array
    {
        return $this->attendanceDataModel->runQuery("SELECT $what FROM {$this->attendanceDataModel->tableName} WHERE $where = $value");
    }

    public function getDataForAPI()
    {
        $query = <<<"SQL"
            SELECT a.id as id, 
                concat(s.first_name, ' ', s.last_name) as full_name,
                a.arrival_time,
                a.departure_time,
                a.date_attended
            FROM attendance AS a
                JOIN  AS s
                ON a.staff_data_id = s.id
            ORDER BY a.staff_data_id, s.id;
        SQL;

        $attendanceResults = $this->attendanceDataModel->runQuery($query, true, true);

        // Get images and then associate them to fetched data

        $attendanceResults = array_map(function ($item) {
            $item["images"] = [];
            $item["images"] = (new StaffDataImage)->getImagesBy($item["id"]);

            return $item;
        }, $attendanceResults);

        return $attendanceResults;
    }

    public function registerAttendance(array $attendanceData)
    {
        if (isset($attendanceData['staff_data_id'])) {
            $this->attendanceDataModel->doInsert($attendanceData);
            return true;
        }
        return false;
    }

    public function updateDepartureTimeAttendance(array $updateAttendanceData, array $queryParams)
    {
        if (isset($queryParams['staff_data_id'])) {
            $stmt = $this->attendanceDataModel->usePDO()->prepare("UPDATE {$this->attendanceDataModel->tableName} SET departure_time = CURRENT_TIME WHERE staff_data_id = :staff_data_id AND date_attended = :date_attended");
            $parameters = [...$updateAttendanceData, ...$queryParams];
            $stmt->execute($parameters);
            return true;
        }

        return false;
    }

    /**
     * Checks whether a staff member attendance has been registered 
     *
     * @param array $values Should contain keys, staff_data_id, date_attended only 
     * @return bool|array
     */
    public function checkAttendanceExistence(array $values, $returnData = false): bool | array
    {
        $dateAttended = isset($values['date_attended']) ? ':date_attended' : 'CURRENT_DATE';
        $stmt = $this->attendanceDataModel->usePDO()->prepare("SELECT * FROM {$this->attendanceDataModel->tableName} WHERE staff_data_id = :staff_data_id AND date_attended = $dateAttended");
        $stmt->execute($values);

        if ($returnData) {
            $fetch_results = $stmt->fetch();
            return $fetch_results ? $fetch_results : [];
        }

        return $stmt->rowCount() ? True : False;
    }

    /**
     * This method will register bulk attendance (in array) 
     *
     * @return bool
     */
    public function mergeAttendanceDataFromFRSystem(array $attendanceBulkData)
    {
        foreach ($attendanceBulkData as $attendanceRecord) {
            # Check whether a users id exists to avoid sql constraint fail 
            if (!$this->staffDataModel->getRecordsBy('id', $attendanceRecord['staff_data_id'], what: ['id']))
                continue;

            // checking whether record exists in database
            $result = $this->checkAttendanceExistence(["staff_data_id" => $attendanceRecord['staff_data_id'], "date_attended" => $attendanceRecord['date_attended']], true);

            // Checking whether there is already an attendance registered, if yes then update the departure_time
            if (count($result)) {
                $this->attendanceDataModel->preparedUpdate(set: ['departure_time' => $attendanceRecord['departure_time']], where: ['staff_data_id' => $attendanceRecord['staff_data_id']]);
                continue;
            }

            // Insert new attendance record
            $this->attendanceDataModel->doInsert($attendanceRecord);
        }

        return True;
    }
}
