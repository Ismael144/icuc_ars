<?php require_once dirname(__DIR__) . "/../../vendor/autoload.php"; ?>

<?php
session_start();

/** Including The Header */

use App\controllers\WebController;
use App\controllers\UserController;

$webController = new WebController;
$userController = new UserController;
$fieldErrors = [];

$pageTitle = "Sign In";
include dirname(__DIR__) . "/../includes/header.php";
?>
<body>

    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card mb-0">
                        <div class="row g-0 align-items-center">
                            <!--end col-->
                            <div class="col-sm-11 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 my-4">
                                        <div class="text-center">
                                            <h5 class="fs-3xl">Create new password</h5>
                                            <p class="text-muted mb-5">Your new password must be different from previous used password.</p>
                                        </div>

                                        <div class="p-2">
                                            <form action="https://themesbrand.com/steex/layouts/auth-signin-basic.html">
                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                    <div id="passwordInput" class="form-text">Your password must be 8-20 characters long.</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="confirm-password-input">Confirm Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Confirm password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="confirm-password-input" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-sm">Password must contain:</h5>
                                                    <p id="pass-length" class="invalid fs-xs mb-2">Minimum <b>8 characters</b></p>
                                                    <p id="pass-lower" class="invalid fs-xs mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-xs mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                    <p id="pass-number" class="invalid fs-xs mb-0">A least <b>number</b> (0-9)</p>
                                                </div>

                                                <div class="form-check form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                                                </div>

                                            </form>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <p class="mb-0">Wait, I remember my password... <a href="auth-signin.html" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
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

<?php include_once dirname(__DIR__)."/../includes/footer.php"; ?>
