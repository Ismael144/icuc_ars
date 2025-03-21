<?php

namespace App\models;

use App\core\Model;

use App\interfaces\ModelInterface;

class Attendance extends Model implements ModelInterface
{
    public readonly string $currentDate;

    public function __construct(
        public string $tableName = "attendance"
    ) {
        $this->currentDate = date("Y-m-d");
    }

    /**
     * Returns the name of the table
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getCurrentlyAttending(): ?array
    {
        return $this->preparedSelect(
            [],
            [
                "date_attended" => date("Y-m-d")
            ]
        );
    }

    public function ifStaffMemberAttended(int $participantId, $returnData = false): null|array|false
    {
        $result = $this->preparedSelect(
            [],
            [
                "date_attended.&" => "CURRENT_DATE",
                "id.&" => $participantId
            ]
        );

        if ($returnData) {
            return count($result) ? true : false;
        }
        return $result;
    }

    public function getTimeElapsed($fixedTime, $startTime) {
        // Validate the fixed time format
        if (!preg_match('/^\d{1,2}:\d{2}$/', $startTime)) {
            return false; // Invalid format
        }
    
        // Parse the fixed time
        $fixedTimeParts = explode(':', $startTime);
        $fixedHour = intval($fixedTimeParts[0]);
        $fixedMinute = intval($fixedTimeParts[1]);
    
        // Get current time
        $startTimeArray = explode(":", $fixedTime);
        $currentHour = $startTimeArray[0];
        $currentMinute = end($startTimeArray);
    
        // Calculate time difference
        $elapsedHours = $currentHour - $fixedHour;
        $elapsedMinutes = $currentMinute - $fixedMinute;
    
        // Adjust for negative elapsed time (current time before fixed time on the same day)
        if ($elapsedHours < 0) {
            $elapsedHours += 24; // Add 24 hours to elapsed hours
        }
    
        // Calculate total elapsed time in minutes
        $totalElapsedMinutes = $elapsedHours * 60 + $elapsedMinutes;
    
        // Calculate the elapsed hours and minutes separately
        $elapsedHours = floor($totalElapsedMinutes / 60);
        $elapsedMinutes = $totalElapsedMinutes % 60;
    
        return [
            'hours' => $elapsedHours,
            'minutes' => $elapsedMinutes
        ];
    }    
}

