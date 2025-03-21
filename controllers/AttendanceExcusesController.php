<?php

namespace App\controllers;

use App\core\Controller;
use App\helpers\RequestDataHelper;
use App\models\StaffData;
use App\models\Attendance;
use App\models\AttendanceExcuse;
use App\helpers\ValidationHelper;

class AttendanceExcusesController extends Controller
{
    public static ValidationHelper $validateHelper;

    public array $excuseArray = [];

    public AttendanceExcuse $attendanceExcuseModel;

    public Attendance $attendanceModel;

    public readonly array $excuseStatus;

    public StaffData $staffDataModel;

    public function __construct()
    {
        $this->staffDataModel = new StaffData;
        $this->attendanceModel = new Attendance;
        self::$validateHelper = new ValidationHelper;
        $this->attendanceExcuseModel = new AttendanceExcuse;

        $this->excuseStatus = [
            1 => "Late",
            2 => "Absent",
            3 => "Tardy"
        ];

        if ($this->requestMethod('post'))
            $this->excuseArray = [
                "status"         => self::$validateHelper->filter($this->handleNull("status", $_POST)),
                "reason"         => self::$validateHelper->filter($this->handleNull("reason", $_POST)),
            ];
    }

    public function mapExcuseStatus(int $statusId): string|false
    {
        return isset($this->excuseStatus[$statusId]) ? $this->excuseStatus[$statusId] : false;
    }

    public function getCurrentUserStaffMember() {
        $staffMember = $this->staffDataModel->getRecordsBy('user_id', AuthUserController::getAuthUser()['id'], false);

        return $staffMember;
    }

    public function doValidations()
    {
        $status = $this->excuseArray['status'];
        $reason = $this->excuseArray['reason'];

        if (empty($status)) {
            $this->setFieldError("status", "This field is required");
        }

        if (empty($reason)) {
            $this->setFieldError("reason", "This field is required, $reason");
        } else {
            if (str_word_count($reason) < 5) {
                $this->setFieldError("reason", "Your reason should be atleast 10 words");
            }
        }
    }

    public function save()
    {
        // A Participant Can Only have one excuse per day
        $newExcuseArray = [
            "staff_data_id" => $this->getCurrentUserStaffMember()['id'],
            "status" => $this->excuseArray['status'],
            "reason" => $this->excuseArray['reason']
        ];

        $this->attendanceExcuseModel->doInsert($newExcuseArray);
    }

    public function getExcusesData()
    {
        $pageNumber = RequestDataHelper::get('page');
        $searchTerm = RequestDataHelper::get('search');

        $attendanceExcusePaginatedData = $this->attendanceExcuseModel->paginateData(totalItemsPerPage: 10, pageNumber: $pageNumber, options: ["isDistinct" => false, "orderBy" => "date_created DESC"]);

        $staffMember = $this->staffDataModel->getRecordsBy('user_id', get('id'), false);
        
        if (isAdmin()) {
            if (!is_null(get('id'))) {
                $attendanceExcusePaginatedData = $this->attendanceExcuseModel->paginateData(totalItemsPerPage: 10, pageNumber: $pageNumber, options: ["isDistinct" => false, "orderBy" => "date_created DESC"], where: ["staff_data_id" => $staffMember['id']]);
            }
        } else {
            $attendanceExcusePaginatedData = $this->attendanceExcuseModel->paginateData(totalItemsPerPage: 10, pageNumber: $pageNumber, options: ["isDistinct" => false, "orderBy" => "date_created DESC"], where: ["staff_data_id" => $this->getCurrentUserStaffMember()['id']]);
        }

        if (!is_null($searchTerm)) {
            $attendanceExcusePaginatedData['data'] = $this->attendanceExcuseModel->search($searchTerm, searchColumns: ["reason", "status"], columns: ["*"]);
        }

        $attendanceExcuseData = array_map(function ($item) {
            $item = [...$item, ...$this->staffDataModel->getRecordsBy("id", $item["staff_data_id"], what: ["concat(first_name, ' ', last_name) as full_name"])];

            return $item;
        }, $attendanceExcusePaginatedData['data']);

        return [...$attendanceExcusePaginatedData, "data" => $attendanceExcuseData];
    }

    public function checkExcuseForStaffMember($staffDataId): bool
    {
        $currentDate = "CURRENT_DATE";

        $query = <<<"SQL"
            SELECT * FROM {$this->attendanceExcuseModel->tableName} WHERE date_created = '{$currentDate}' AND staff_data_id = :staffDataId;
        SQL;

        $stmt = $this->attendanceExcuseModel->usePDO()->prepare($query);
        $stmt->execute(["staffDataId" => $staffDataId]);

        return $stmt->rowCount() ? true : false;
    }

    public function getSingleExcuse(int $excuseId): array|bool
    {
        $singleExcuse = $this->attendanceExcuseModel->getSingleExcuse($excuseId);

        if (is_bool($singleExcuse)) return false;

        $singleExcuse = [...$singleExcuse, ...$this->staffDataModel->getRecordsBy("id", $singleExcuse["staff_data_id"], false, ["concat(first_name, ' ', last_name) as full_name"])];

        return $singleExcuse;
    }

    /**
     * Updates an excuse in the attendance excuse model.
     *
     * @param int $excuseId The ID of the excuse to update.
     * @return mixed The results of the update operation.
     */
    public function updateExcuse(int $excuseId)
    {
        $results = $this->attendanceExcuseModel->updateExcuse(["reason" => $this->excuseArray["reason"], "status" => $this->excuseArray["status"]], ["id" => $excuseId]);

        return $results;
    }
}
