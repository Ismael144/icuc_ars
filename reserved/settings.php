<?php
$pageTitle = "Settings Page";
include dirname(__DIR__)."/includes/header.php";

authProtect("auth/signin");

use App\controllers\UserController;
use App\controllers\WebController;

$assetsPath = "";
$userController = new UserController;
$websiteController = new WebController;

$freeDiskSpace = function ($withUnits = false) use ($websiteController) {
    return $websiteController->displayStorage($withUnits)["free_space"];
};
$totalDiskSpace = function ($withUnits = false) use ($websiteController) {
    return $websiteController->displayStorage($withUnits)["total_space"];
};

include dirname(__DIR__)."/includes/layouts/sidebar.php";
include dirname(__DIR__)."/includes/layouts/topbar.php";
?>

<div class="main-content">
    <div class="page-content">
        <div class="container">

            <div class="row justify-content-center">

                <div class="col-xl-10 mt-3">

                    <div class="row">

                        <div class="col-xl-9">

                            <div id="general" class="mb-5">
                                <h4><i class="far fa-user fa-fw text-body text-opacity-50 me-1"></i> General</h4>
                                <p>View and update your general account information and settings.</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Name</div>
                                                <div class="text-body text-opacity-50">Sean Ngu</div>
                                            </div>
                                            <div class="w-100px">
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Username</div>
                                                <div class="text-body text-opacity-50">@seantheme</div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Phone</div>
                                                <div class="text-body text-opacity-50">+1-202-555-0183</div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Email address</div>
                                                <div class="text-body text-opacity-50">support@seantheme.com</div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Password</div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="notifications" class="mb-5">
                                <h4><i class="far fa-bell fa-fw text-body text-opacity-50 me-1"></i> Notifications</h4>
                                <p>Enable or disable what notifications you want to receive.</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Comments</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-success me-1"></i> Enabled (Push, SMS)
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Tags</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw me-1"></i> Disabled
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Reminders</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-success me-1"></i> Enabled (Push, Email, SMS)
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>New orders</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-success me-1"></i> Enabled (Push, Email, SMS)
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="privacyAndSecurity" class="mb-5">
                                <h4><i class="far fa-copyright fa-fw text-body text-opacity-50 me-1"></i> Privacy and security</h4>
                                <p>Limit the account visibility and the security settings for your website.</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Who can see your future posts?</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    Friends only
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Photo tagging</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-success me-1"></i> Enabled
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Location information</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-muted me-1"></i> Disabled
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Firewall</div>
                                                <div class="text-body text-opacity-50 d-block d-xl-flex align-items-center">
                                                    <div class="d-flex align-items-center justify-content-between"><i class="fa fa-circle fs-8px fa-fw text-muted me-1"></i> Disabled</div>
                                                    <span class="bg-warning-transparent-1 text-warning ms-xl-3 mt-1 d-inline-block mt-xl-0 px-1 rounded-sm">
                                                        <i class="fa fa-exclamation-circle text-warning fs-12px me-1"></i>
                                                        <span class="text-warning">Please enable the firewall for your website</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="payment" class="mb-5">
                                <h4><i class="far fa-credit-card fa-fw text-body text-opacity-50 me-1"></i> Payment</h4>
                                <p>Manage your website payment provider</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Allowed payment method</div>
                                                <div class="text-body text-opacity-50">
                                                    Paypal, Credit Card, Apple Pay, Amazon Pay, Google Wallet, Alipay, Wechatpay
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="shipping" class="mb-5">
                                <h4><i class="far fa-paper-plane fa-fw text-body text-opacity-50 me-1"></i> Shipping</h4>
                                <p>Allowed shipping area and zone setting</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Allowed shipping method</div>
                                                <div class="text-body text-opacity-50">
                                                    Local, Domestic
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="mediaAndFiles" class="mb-5">
                                <h4><i class="far fa-images fa-fw text-body text-opacity-50 me-1"></i> Media and Files</h4>
                                <p>Allowed files and media format upload setting</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Allowed files and media format</div>
                                                <div class="text-body text-opacity-50">
                                                    .png, .jpg, .gif, .mp4
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Media and files cdn</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-muted me-1"></i> Disabled
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="languages" class="mb-5">
                                <h4><i class="fa fa-language fa-fw text-body text-opacity-50 me-1"></i> Languages</h4>
                                <p>Language font support and auto translation enabled</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Language enabled</div>
                                                <div class="text-body text-opacity-50">
                                                    English (default), Chinese, France, Portuguese, Japense
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Auto translation</div>
                                                <div class="text-body text-opacity-50 d-flex align-items-center justify-content-between">
                                                    <i class="fa fa-circle fs-8px fa-fw text-success me-1"></i> Enabled
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="system" class="mb-5">
                                <h4><i class="far fa-hdd fa-fw text-body text-opacity-50 me-1"></i> System</h4>
                                <p>System storage, bandwidth and database setting</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Web storage</div>
                                                <div class="text-body text-opacity-50">

                                                    <div class="d-flex align-items-center py-2" style="margin-top: 10px;">
                                                        <div class="flex-grow-1" style="width: 300px !important;">
                                                            <div class="progress animated-progress custom-progress progress-label">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= 100 - round(($freeDiskSpace() / $totalDiskSpace()) * 100) ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                                    <div class="label"><?= 100 - round(($freeDiskSpace() / $totalDiskSpace()) * 100) ?>%</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="disk-props mt-2 d-flex align-items-center justify-content-between">
                                                        <div class="disk-free d-flex align-items-center gap-1">
                                                            Free <span class="bg-light px-1" style="border-radius: 5px;  font-weight: bold;"><?= $freeDiskSpace(true) ?></span>
                                                        </div>
                                                        <div class="disk-total d-flex align-items-center gap-1">
                                                            Total <span class="bg-light px-1" style="border-radius: 5px; font-weight: bold;"><?= $totalDiskSpace(true) ?></span>
                                                        </div>
                                                    </div>
                                                    <!-- " -->
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-outline-light py-2 px-4" style="color: black; ">Manage</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Monthly bandwidth</div>
                                                <div class="text-body text-opacity-50">
                                                    Unlimited
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Database</div>
                                                <div class="text-body text-opacity-50">
                                                    MySQL version <?= $websiteController->getSQLVersion() ?>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#modalEdit" data-bs-toggle="modal" class="btn btn-default w-100px disabled">Update</a>
                                            </div>
                                        </div>
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Platform</div>
                                                <div class="text-body text-opacity-50">
                                                    PHP Version <?= PHP_VERSION ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="resetSettings" class="mb-5">
                                <h4><i class="fa fa-redo fa-fw text-body text-opacity-50 me-1"></i> Reset settings</h4>
                                <p>Reset all website setting to factory default setting.</p>
                                <div class="card">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="flex-1 text-break">
                                                <div>Reset Settings</div>
                                                <div class="text-body text-opacity-50">
                                                    This action will clear and reset all the current website setting.
                                                </div>
                                            </div>
                                            <div>
                                                <a href="#" class="btn btn-outline-light py-2 px-4" style="color: black; ">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="col-xl-3">

                            <nav id="sidebar-bootstrap" class="navbar navbar-sticky d-none d-xl-block sticky-top settings-side-nav" style="top: 70px !important; z-index: 1 !important;">
                                <style>
                                    .settings-side-nav .nav-link {
                                        padding-top: 4px !important;
                                        padding-bottom: 4px !important;
                                    }
                                </style>
                                <nav class="nav d-flex flex-column" style="font-size: 15px;">
                                    <a class="nav-link active" href="#general" data-toggle="scroll-to">General</a>
                                    <a class="nav-link" href="#notifications" data-bs-toggle="scroll-to">Notifications</a>
                                    <a class="nav-link" href="#privacyAndSecurity" data-bs-toggle="scroll-to">Privacy and security</a>
                                    <a class="nav-link" href="#payment" data-bs-toggle="scroll-to">Payment</a>
                                    <a class="nav-link" href="#shipping" data-bs-toggle="scroll-to">Shipping</a>
                                    <a class="nav-link" href="#mediaAndFiles" data-bs-toggle="scroll-to">Media and Files</a>
                                    <a class="nav-link" href="#languages" data-bs-toggle="scroll-to">Languages</a>
                                    <a class="nav-link" href="#system" data-bs-toggle="scroll-to">System</a>
                                    <a class="nav-link" href="#resetSettings" data-bs-toggle="scroll-to">Reset settings</a>
                                </nav>
                            </nav>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<?php include_once dirname(__DIR__)."/includes/footer.php"; ?>
