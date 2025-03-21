<?php
$pageTitle = "User Profile Page";
include_once dirname(__DIR__) . "/../includes/header.php";

use App\controllers\UserController;

authProtect("../../auth/signin");
$userController = new UserController;
?>

<div id="layout-wrapper">
  <!-- ========== App Menu ========== -->
  <?php include dirname(__DIR__) . "/../includes/layouts/topbar.php" ?>
  <?php include dirname(__DIR__) . "/../includes/layouts/sidebar.php" ?>

  <div class="main-content">
    <div class="page-content d-flex justify-content-center">
      <div class="col-12">
        <?php
        $imgPath = !strlen($authUser['avatar']) ? "{$appDirPath}assets/images/male-img.jpg" : "/icuc_ars/images/users/{$authUser['avatar']}"
        ?>
        <div class="card border shadow-none mt-4 col-12">
          <div class="card-header border-bottom">
            <h5 class="card-title py-2">
              Personal Information
            </h5>
          </div>
          <!-- <div class="card-header">
            <div class="c-img">
              <img src="<?= $imgPath ?>" class="card-img-top" alt="Image" style="width: 120px; height: 120px; object-fit: cover; object-position: center; border-radius: 50%;">
              <div class="user-bio">
                <div>
                  <b>Name</b>
                  <div class="px-1 py-2" style="background: #eee; border-radius: 5px;">
                    <?= $authUser['username'] ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="user-email bg-light p-2" style="border-radius: 5px;">
              <small class="label" style="font-weight: bold">Email</small>
              <p class="card-text my-1"><?= $authUser["email"] ?></p>
            </div>
            <div class="department text-capitalize my-2">
              <b>Under The '<?= $authUser["dept"] ?>' Department</b>
            </div>
          </div>
          <div class="card-footer">
            <div>
              <a href="profile_change" class="edit">Edit Profile</a>
            </div>
            <div>
              <a href="password_change" class="change-password">Change Password</a>
            </div>
          </div> -->

          <div class="user-card" style="box-shadow: 0;">
            <div class="card p-3">
              <div class="card-image">
                <img src="<?= userImage($authUser['avatar']) ?>" alt="" width="120px" height="120px" style="border-radius: 50%;">
              </div>
              <div class="content row">
                <div class="col">
                  <div class="field my-3">
                    <b class="small my-1">Full Name</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['username'] ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Email Address</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['email'] ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Phone Number</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['phone_number'] ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Gender</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['role'] ?>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="field my-3">
                    <b class="small my-1">Last Login</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $userController->_format_date_and_time($authUser['last_login']) ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Department</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['department'] ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Role</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $authUser['role'] ?>
                    </div>
                  </div>
                  <div class="field my-3">
                    <b class="small my-1">Date Created</b>
                    <div class="bg-light px-2 py-2 my-1" style="border-radius: 3px;">
                      <?= $userController->format_date($authUser['date_created']) ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php if (isAdmin() || $authUser['id'] == $authUser['id']) : ?>
                <div class="d-flex align-items-center gap-2">
                  <div>
                    <a href="profile_change" class="btn btn-sm btn-primary d-flex align-items-center gap-2" style="display: inline-block;">
                      <span>
                        Change Profile
                      </span>
                    </a>
                  </div>
                  <div>
                    <a href="password_change" class="d-flex btn btn-secondary btn-sm align-items-center gap-2" style="display: inline-block;">
                      <span>
                        Change Password
                      </span>
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>