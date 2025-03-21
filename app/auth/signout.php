<?php

use App\controllers\AuthUserController;

$pageTitle = "User Sign Out";
include dirname(__DIR__)."/includes/header.php";

AuthUserController::$sessionHelper->set(_userauth__success: 'User Sign Out: You signed out successfully');

authProtect("signin");

use App\enums\UserStatus;

if (session_id() !== "") {
    $webController->sessionHelper->destroyAll();
    $authUserController->userModel->updateStatus(UserStatus::INACTIVE);
}

?>

<body>

    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="mb-0">
                        <div class="row g-0 align-items-center">
                            <!--end col-->
                            <div class="col-sm-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="display-5 text-center">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </div>
                                        <div class="mt-4 pt-2 text-center">
                                            <h5 class="fs-2xl">You are Signed Out</h5>
                                            <p class="text-muted">Thank you for using ICUC CRM System , click to sign in again if you wish to<span class="fw-semibold"></span> </p>
                                            <div class="mt-4">
                                                <a href="/icuc_ars/app/auth/signin" class="btn btn-success w-100">Sign In</a>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->

        
    </section>

    <?php include_once dirname(__DIR__)."/includes/footer.php"; ?>