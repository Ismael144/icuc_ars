<?php

/** Including The Header */

use App\controllers\AuthUserController;
use App\controllers\WebController;

$pageTitle = "Sign In";

include dirname(__DIR__) . "/includes/header.php";

$webController = new WebController;

$email = handleNull('email', $authUserController->user);
$password = handleNull('password', $authUserController->user);

# Redirect On Auth
if (AuthUserController::isAuthenticated()) $authUserController->redirect("../dashboard");

if (requestMethod('post')) {
    # Validate user sign in form
    $authUserController->signInValidations();

    if ($authUserController->noErrors()) {
        if ($authUserController->signIn()) {
            $authUser = AuthUserController::getAuthUser();
            AuthUserController::$sessionHelper->set(_userauth__success: "Welcome {$authUser['username']}!: You successfully signed in to the ICUC ARM System.");
            $authUserController->redirect("../dashboard");
        }
    }
}

?>

<body>
    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card mb-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-xxl-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center mt-2">
                                            <div class="logo-img mb-3">
                                                <img src="/icuc_ars/app/assets/images/logo.png" width="100" alt="">
                                            </div>
                                            <h5 class="fs-3xl">Welcome Back</h5>
                                            <p class="text-muted">Sign in to continue to The ICUC Dashboard.</p>
                                        </div>
                                        <div class="p-2 mt-3">
                                            <div class="text-danger text-center mb-2"><?= $authUserController->getFieldErr("signinError") ?></div>
                                            <form action="" method="POST" novalidate>
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Email Address<span class="text-danger">*</span></label>
                                                    <div class="position-relative ">
                                                        <input type="email" class="form-control password-input" id="email" placeholder="Enter Your Email Address" id="someInvalidEmail" name="email" value="<?= $email ?>">
                                                    </div>
                                                    <span class="invalid-feedback">
                                                        Hello world
                                                    </span>
                                                    <span class="text-danger"><?= $authUserController->getFieldErr("email") ?></span>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="change/sendEmail" class="text-muted">Forgot password?</a>
                                                    </div>
                                                    <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input " placeholder="Enter password" id="password-input" name="password" value="<?= $password ?>">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>

                                                        <span class="text-danger"><?= $authUserController->getFieldErr("password") ?></span>

                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Sign In</button>
                                                </div>
                                            </form>
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

    <?php include dirname(__DIR__) . "/includes/footer.php" ?>