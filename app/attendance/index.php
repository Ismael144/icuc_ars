<?php

use App\controllers\AttendanceDataController;

$pageTitle = "Staff Currently Attending";
include dirname(__DIR__) . "/includes/header.php";
$attendanceDataController = new AttendanceDataController;
?>

<?php include dirname(__DIR__) . "/includes/layouts/topbar.php" ?>
<?php include dirname(__DIR__) . "/includes/layouts/sidebar.php" ?>

<div class="main-content">
    <div class="page-content">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Currently Attending</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/app/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="../index">Attendance</a></li>
                                <li class="breadcrumb-item active">Currently Attending</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header my-2">
                    <h5 class="card-title">Currently Attending</h4>
                </div>
                <div class="card-body pt-1">
                    <div class="d-flex align-items-center flex-grow-1 justify-content-between mb-3">
                        <div class="operations d-flex align-items-center gap-2 justify-content-between w-100">
                            <style>
                                .link-list {
                                    list-style: none;
                                    margin: 0 7px;
                                    /* font-weight: 500; */
                                }

                                .link-list a {
                                    color: #444;
                                }
                            </style>
                            <div class="dropdown open">
                                <button class="btn btn-light dropdown-toggle" type="button" id="more-items-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    More
                                </button>
                                <div class="dropdown-menu mt-2" aria-labelledby="more-items-dropdown">
                                    <li class="link-list">
                                        <a href="holidays">Holidays</a>
                                    </li>
                                    <div class="dropdown-divider"></div>
                                    <li class="link-list">
                                        <a href="excuses">Excuses</a>
                                    </li>
                                    <div class="dropdown-divider"></div>
                                    <li class="link-list">
                                        <a href="analysis">Attendance Analysis</a>
                                    </li>
                                </div>
                            </div>
                            <?php if (isAdmin()) : ?>
                                <div>
                                    <a href="data" class="btn btn-light">All Attendances</a>
                                </div>
                            <?php else : ?>
                                <div>
                                    <a href="data?staff=<?= $authUser['id'] ?>" class="btn btn-light">My Attendances</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-2">
                        <b class="attendance-date mb-2" style="font-size: 16px;">
                            Today Is <?= $attendanceDataController->format_date(date('Y-m-d')) ?>
                        </b>
                    </div>
                    <div class="row" id="attendance-cards-container">
                        <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                            <div class="spinner-border text-success" style="width: 80px; height: 80px;"></div>
                            <div class="text-dark mt-1" style="font-weight: bold;">Loading Data, Please Wait ...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= $appDirPath ?>assets/js/currentAttendance.js"></script>
<script>
    // Instantiate the AttendanceManager class
    const attendanceManager = new AttendanceManager();
</script>