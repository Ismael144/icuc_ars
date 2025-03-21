<?php

namespace App\controllers;

use App\core\Controller;
use App\models\Attendance;
use App\models\StaffData;
use App\models\AttendanceExcuse;
use App\models\AttendanceHoliday;
use App\models\UserDepartment;
use App\models\User;

/**
 * 
 * AttendanceAnalysisController, 
 * The logic will be splitted into two
 * Logic for each department and the one for each individual staff member
 * 
 */
class AttendanceDataAnalysisController extends Controller
{
  public User $userModel;

  public StaffData $staffDataModel;

  public UserDepartment $userDeptModel;

  public Attendance $attendanceDataModel;

  public AttendanceExcuse $attendanceExcusesModel;

  public AttendanceHoliday $attendanceHolidaysModel;

  public function __construct(public readonly ?int $staffMemberId = null, public readonly ?int $departmentId = null, public ?string $dateFilter = null)
  {
    $this->userModel = new User;
    $this->staffDataModel = new StaffData;
    $this->userDeptModel = new UserDepartment;
    $this->attendanceDataModel = new Attendance;
    $this->attendanceExcusesModel = new AttendanceExcuse;
    $this->attendanceHolidaysModel = new AttendanceHoliday;
  }

  /*
   *  --------------------------------- 
   * |    LOGIC FOR EACH DEPARTMENT    |
   *  -------------------------  -------- 
   */

  public function getDepartmentInfo(): array
  {
    $results = $this->userDeptModel->getRecordsBy("id", $this->departmentId, multiple: false);
    return $results;
  }

  public function getStaffMembersDataInGivenDept(array $columnsNeeded = ["*"])
  {
    $usersInGivenDept = $this->userModel->getRecordsBy("dept_id", $this->departmentId, multiple: true);

    $staffMembersOfGivenDept = [];

    foreach ($usersInGivenDept as $user) {
      $staffDataRecord = $this->staffDataModel->getRecordsBy("user_id", $user["id"], multiple: false);

      if (!$staffDataRecord)
        continue;

      $staffMembersOfGivenDept[] = [...$this->staffDataModel->getRecordsBy("user_id", $user["id"], multiple: false, what: $columnsNeeded), "user_info" => $user];
    }

    return $staffMembersOfGivenDept;
  }


  public function getAttendanceDataForUsersInGivenDept(array $columnsNeeded = ['*'])
  {
    $staffMembersInGivenDept = $this->getStaffMembersDataInGivenDept();
    $attendanceData = [];

    foreach ($staffMembersInGivenDept as $staffMember) {
      $attendanceData = $this->attendanceDataModel->getRecordsBy("staff_data_id", $staffMember["id"], multiple: true, what: $columnsNeeded);
    }

    return $attendanceData;
  }


  public function getAttendanceRateForGivenDept($year)
  {
    $data = $this->getAttendanceDataForUsersInGivenDept();

    $groupedData = [];
    foreach ($data as $record) {
      $dateAttended = $record['date_attended'];
      $recordYear = substr($dateAttended, 0, 4);
      if ($recordYear != $year) {
        continue; // Skip records not matching the requested year
      }

      $month = substr($dateAttended, 5, 2);
      $key = "$year-$month";

      if (!isset($groupedData[$key])) {
        $groupedData[$key] = [];
      }

      $groupedData[$key][] = $record;
    }

    return $groupedData;
  }
  public function numberOfStaffMembersInGivenDept(): int
  {
    $staffMemberCount = $this->getStaffMembersDataInGivenDept(["count(id) as staff_member_count"]);
    return (int)$staffMemberCount["staff_member_count"];
  }

  public function getAttendanceExcusesInformationForGivenDept()
  {
    $staffMembersOfGivenDept = $this->getStaffMembersDataInGivenDept(["id"]);

    $staffMemberExcuses = [];

    foreach ($staffMembersOfGivenDept as $staffMember) {
      $staffMemberExcuses[] = $this->attendanceExcusesModel->getRecordsBy("staff_data_id", $staffMember["id"], multiple: true);
    }

    return $staffMemberExcuses;
  }

  public function getTotalAttendanceTimeForGivenDept()
  {
    $staffAttendanceData = $this->getAttendanceDataForUsersInGivenDept();

    $attendanceTimeData = [];

    $attendanceTimeData['hours'] = 0;

    foreach ($staffAttendanceData as $dataRecord) {
      if (!empty($dataRecord)) {
        $dateDiff = $this->dateTimeDifference($dataRecord["arrival_time"], $dataRecord["departure_time"]);
        $attendanceTimeData['hours'] += $dateDiff->h;
      }
    }

    $attendanceTimeData["days"] = count($staffAttendanceData);
    $attendanceTimeData["weeks"] = floor($attendanceTimeData["days"] / 7);
    $attendanceTimeData["months"] = floor($attendanceTimeData["weeks"] / 4);

    return $attendanceTimeData;
  }

  public function getNumberOfHolidaysAttendedOrUnattended()
  {
    $staffAttendanceData = $this->getAttendanceDataForUsersInGivenDept();

    $holidays = $this->attendanceHolidaysModel->fetchColumns(["id", "name", "date"]);

    $holidaysInfoData = [];

    $holidaysInfoData["attended"] = [];
    $holidaysInfoData["unattended"] = [];

    foreach ($staffAttendanceData as $staffRecord) {
      foreach ($holidays as $holiday) {
        print($holiday["date"] == $staffRecord["date_attended"]);
        if ($holiday["date"] == $staffRecord["date_attended"] && !in_array($holiday, $holidaysInfoData["attended"]))
          $holidaysInfoData["attended"][] = $holiday;
        else if ($holiday["date"] != $staffRecord["date_attended"] && !in_array($holiday, $holidaysInfoData["unattended"]))
          $holidaysInfoData["unattended"][] = $holiday;
      }
    }

    $holidaysInfoData["attended_count"] = count($holidaysInfoData["attended"]);
    $holidaysInfoData["unattended_count"] = count($holidaysInfoData["unattended"]);

    var_dump($holidaysInfoData);

    return $holidaysInfoData;
  }

  public function getNumberOfHolidaysAttendedOrUnattendedFilteredByDate($periodType = "day", $period = "2024-02-13")
  {
    if ($periodType == "day") {
      // Logic to handle getting data for a specific day

      // 1. Fetch holidays for the given date
      $holidays = $this->attendanceHolidaysModel->fetchColumns(["id", "name", "date"]);

      // 2. Fetch attendance data for staff in the department for the given day
      $staffAttendanceData = $this->getAttendanceDataForUsersInGivenDept(
        // ["date_attended", "staff_data_id"] // Include necessary columns
      );

      // 3. Process data to find attended/unattended holidays
      $holidaysInfoData = [
        "attended" => [],
        "unattended" => [],
      ];

      foreach ($holidays as $holiday) {
        $attended = false;
        foreach ($staffAttendanceData as $staffRecord) {
          if ($holiday["date"] == $staffRecord["date_attended"] && !in_array($holiday, $holidaysInfoData["attended"])) {
            $holidaysInfoData["attended"][] = $holiday;
            $attended = true;
            break; // Only count once per holiday for a staff member
          }
        }
        if (!$attended && !in_array($holiday, $holidaysInfoData["unattended"])) {
          $holidaysInfoData["unattended"][] = $holiday;
        }
      }

      // 4. Calculate attended and unattended count
      $holidaysInfoData["attended_count"] = count($holidaysInfoData["attended"]);
      $holidaysInfoData["unattended_count"] = count($holidaysInfoData["unattended"]);

      return $holidaysInfoData;
    } else {
      // Handle other period types (e.g., week, month) if needed
      throw new \InvalidArgumentException("Unsupported period type: $periodType");
    }
  }

  public function getAttendanceRateForDepartment()
  {
  }


  public function departmentWiseAttendanceLeaderBoard()
  {
    $staffMembersData = $this->getStaffMembersDataInGivenDept();

    $attendanceTimeData = [];

    $totalHoursForStaffMembers = [];

    foreach ($staffMembersData as $staffMember) {
      $staffMemberAttendanceData = $this->attendanceDataModel->getRecordsBy("staff_data_id", $staffMember["id"], multiple: true);

      $totalHours = 0;

      foreach ($staffMemberAttendanceData as $dataRecord) {
        if (!empty($dataRecord)) {
          $dateDiff = $this->dateTimeDifference($dataRecord["arrival_time"], $dataRecord["departure_time"]);
          $totalHours += $dateDiff->h;
        }
      }

      $rankCalculation = (int)($totalHours - count($this->attendanceExcusesModel->getRecordsBy("staff_data_id", $staffMember["id"], multiple: true)));
      $totalHoursForStaffMembers[$staffMember['id']] = $rankCalculation;
      echo $staffMember['id'];
      $totalHours = 0;
    }

    rsort($totalHoursForStaffMembers, SORT_DESC);

    return $totalHoursForStaffMembers;
  }

  /* ------------ END -------------- */

  /*
   *  ------------------------------------
   * |        LOGIC FOR EACH USER         |
   *  ------------------------------------
   */

  public function getStaffMemberById(): array
  {
    $staffMember = $this->staffDataModel->getRecordsBy("id", $this->staffMemberId, multiple: false);
    $staffMember["user_info"] = $this->userModel->getRecordsBy("id", $staffMember["user_id"], multiple: false);

    return $staffMember;
  }

  public function getTotalAttendanceTimeForStaffMember(): array
  {
    $staffAttendanceData = $this->attendanceDataModel->getRecordsBy("staff_data_id", $this->staffMemberId, multiple: true);

    $attendanceTimeData = [];

    $attendanceTimeData['hours'] = 0;

    foreach ($staffAttendanceData as $dataRecord) {
      if (!empty($dataRecord)) {
        $dateDiff = $this->dateTimeDifference($dataRecord["arrival_time"], $dataRecord["departure_time"]);
        $attendanceTimeData['hours'] += $dateDiff->h;
      }
    }

    $attendanceTimeData["days"] = count($staffAttendanceData);
    $attendanceTimeData["weeks"] = floor($attendanceTimeData["days"] / 7);
    $attendanceTimeData["months"] = floor($attendanceTimeData["weeks"] / 4);

    return $attendanceTimeData;
  }
}
