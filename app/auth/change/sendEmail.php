<?php
/* Including The Header */
$pageTitle = "Email Search";
include dirname(__DIR__) . "/../includes/header.php"
?>

<?php

use App\controllers\EmailServiceController;

$emailServiceController = new EmailServiceController;

$genError = "";

$feedback = [];

if(isset($_SESSION['email'])) $authUserController->user["email"] = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # Email Form Validation 
    if (!empty($authUserController->user['email'])) {
        if (!$authUserController::$validateHelper->validateEmail($authUserController->user['email'])) $authUserController->fieldErrors['email'] = "You entered an invalid email";
    } else $authUserController->fieldErrors['email'] = "This field is required";

    # Searching for the email address
    if (empty($authUserController->fieldErrors)) {
        if ($authUserController->emailSearch($authUserController->user['email'])) {
            # Sent The Code
            if ($emailServiceController->generateAndSendVerificationCode($authUserController->user["email"])) {
                session_redirect("verificationCode", ["_auth_change_password__success" => "Verification Code Sent: The verification code was sent successfully, check your email..."]);
            } else {
                $genError = $errors;
            }
        }
        $feedback['feedback'] = 'The Code Is Sent Successfully';
        $webController->redirect("verificationCode");
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
                            <div class="col-xxl-7 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center mt-2">
                                            <div class="pb-4">
                                                <img src="/icuc_ars/app/assets/images/logo.png" alt="" class="avatar-md">
                                            </div>
                                            <h5 class="fs-3xl">Enter Your Email</h5>
                                            <p class="text-muted mb-4">Enter your email address to receive verification code</p>
                                        </div>

                                        <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                            Enter your email here to receive the verification code
                                        </div>
                                        <div class="p-2">
                                            <form method='POST'>
                                                <div class="mb-1">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control password-input" id="email" placeholder="Enter Your Email Address" value="<?= $authUserController->handleNull("email", $authUserController->user) ?>">
                                                    <div class="text-danger my-2">
                                                        <?= $authUserController->handleNull("email", $authUserController->fieldErrors) ?>
                                                    </div>
                                                </div>
                                                <div class="text-danger my-1">
                                                    <?= $genError ?>
                                                </div>
                                                <div class="text-center mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Send Verification Code</button>
                                                </div>
                                            </form><!-- end form -->
                                        </div>
                                        <div class="mt-4 text-center">
                                            <p class="mb-0">Wait, I remember my password, <a href="../signin" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
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

<?php include_once "../../includes/footer.php"; ?>