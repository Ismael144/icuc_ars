<?php

namespace App\controllers;

use App\core\Controller;
use App\models\Notification;
use App\helpers\ValidationHelper;
use App\helpers\RequestDataHelper;
use App\controllers\AuthUserController;

class NotificationsController extends Controller
{
  public Notification $notificationsModel;

  public array $notificationsForm = [];

  public UserController $userController;

  public ValidationHelper $validateHelper;

  public function __construct()
  {
    $this->userController = new UserController;
    $this->notificationsModel = new Notification;
    $this->validateHelper = new ValidationHelper;

    if (RequestDataHelper::method('POST')) {
      $this->notificationsForm = [
        "title" => $this->validateHelper->filter(handleNull("title", $_POST)),
        "body"   => $this->validateHelper->filter(handleNull("body", $_POST)),
      ];
    }
  }

  public function doValidations()
  {
    [
      "title" => $title,
      "body" => $body,
    ] = $this->notificationsForm;

    if (empty($title)) {
      $this->setFieldError("title", "This field is required...");
    }

    if (empty($body)) {
      $this->setFieldError("body", "This field is required...");
    }
  }

  public function create()
  {
    $notificationData = [
      ...$this->notificationsForm,
      "user_id" => AuthUserController::getAuthUser()["id"]
    ];

    $results = $this->notificationsModel->create($notificationData);
    return $results;
  }

  public function update(int $notificationId)
  {
    $results = $this->notificationsModel->update([...$this->notificationsForm, "date_created" => date("Y-m-d H:i:s"), "is_read" => 0], ["id" => $notificationId]);
    return $results;
  }

  public function deleteNotification(int $notificationId)
  {
    $results = $this->notificationsModel->delete($notificationId);
    return $results;
  }

  public function getAll()
  {
    $notificationData = $this->notificationsModel->getAll();
    $paginatedDateCreated = $this->notificationsModel->paginateData(totalItemsPerPage: 5, pageNumber: get('page'), options: ["isDistinct" => true, "orderBy" => "date_created DESC"], columns: ['date_created']);


    if (!is_null(get('author'))) {
      $notificationData = $this->notificationsModel->preparedSelect(select: [], where: ["user_id" => get('author')], single: false);

      $notificationData = array_map(function ($item) {
        $item["user"] = $this->userController->userModel->getRecordsBy("id", $item["user_id"], what: ["avatar", "username", "uniqid", "id"]);
        return $item;
      }, $notificationData);

      return $notificationData;
    }

    $notificationData = array_map(function ($item) {
      $item["user"] = $this->userController->userModel->getRecordsBy("id", $item["user_id"], what: ["avatar", "username", "uniqid", "id"]);
      return $item;
    }, $notificationData);

    $newNotificationData = array_map(function ($item) use ($notificationData) {
      foreach ($notificationData as $notificationItem) {
        if ($item["date_created"] == $notificationItem["date_created"]) {
          $item["notifications"][] = $notificationItem;
        }
      }
      return $item;
    }, $paginatedDateCreated['data']);

    $paginatedDateCreated["data"] = $newNotificationData;

    return $paginatedDateCreated;
  }

  public function search($searchTerm = "")
  {
    $searchNotificationsData = $this->notificationsModel->paginateData(10, search: ["title" => $searchTerm, "body" => $searchTerm]);

    $searchNotificationsData["data"] = array_map(function ($item) {
      $item["user"] = $this->userController->userModel->getRecordsBy("id", $item["user_id"], what: ["avatar", "username", "uniqid", "id"]);
      return $item;
    }, $searchNotificationsData["data"]);

    return $searchNotificationsData;
  }


  public function time_ago_string($past_time)
  {
    $now = new \DateTime();
    $past = new \DateTime($past_time);
    $difference = $now->diff($past);

    if ($difference->y > 0) {
      return $difference->y . " years ago";
    } elseif ($difference->m > 0) {
      return $difference->m . " months ago";
    } elseif ($difference->d > 0) {
      return $difference->d . " days ago";
    } elseif ($difference->h > 0) {
      return $difference->h . " hours ago";
    } elseif ($difference->i > 0) {
      return $difference->i . " minutes ago";
    } else {
      return "just now";
    }
  }


  public function latestNotifications(): array
  {
    $results = $this->notificationsModel->runQuery("SELECT * FROM {$this->notificationsModel->tableName} WHERE is_read = 0 ORDER BY date_created DESC LIMIT 8", multiple: true);

    $notificationData = array_map(function ($item) {
      $item["user"] = $this->userController->userModel->getRecordsBy("id", $item["user_id"], what: ["avatar", "username", "uniqid", "id"]);
      return $item;
    }, $results);

    return ["latest_notifications" => $notificationData, "notifications_count" => count($results)];
  }
}
