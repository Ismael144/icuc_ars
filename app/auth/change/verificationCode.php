<?php require_once dirname(dirname(__DIR__)) . "/../vendor/autoload.php"; ?>

<?php
session_start();

/** Including The Header */

use App\controllers\WebController;
use App\controllers\UserController;
use App\controllers\EmailServiceController;

$webController = new WebController;
$userController = new UserController;

$pageTitle = "Enter Verification Code...";

include dirname(__DIR__) . "/../includes/header.php";

$genError = "";

$emailServiceController = new EmailServiceController;

// To Invalidate the session verification code

// Get the time when the action started
$action_start_time = $_SESSION['timeGenerated'];

// Perform your actions here...

// After some time has passed, check if three hours have elapsed
$current_time = time();
$time_elapsed = $current_time - $action_start_time;
$three_hours_in_seconds = .5 * 60 * 60; // 3 hours * 60 minutes * 60 seconds

if ($time_elapsed >= $three_hours_in_seconds) {
    $_SESSION = [];
}

if (!isset($_SESSION['email'])) {
    session_redirect("sendEmail", ["_verification_code__notice" => "The Verification Code expired, please try again..."]);
}

if (filter_has_var(INPUT_POST, "confirm_code")) {
    # Email Form Validation 
    if (empty($userController->user['verificationCode'])) {
        $userController->fieldErrors['verificationCode'] = "This field is required";
    }

    # Searching for the email address
    if (empty($userController->fieldErrors)) {
        if ($userController->user["verificationCode"] == $_SESSION["verificationCode"]) {
            $_SESSION["is_verified"] = true;
            session_redirect("passwordChange", ["_password_change__success" => "Verification Successful: You successfully verified your email..."]);
        } else {
            $userController->fieldErrors['verificationCode'] = "Sorry, you entered the wrong verification code, please double check the email that was sent to you...";
            $userController->user["verificationCode"] = "";
        }
    }
}

if (filter_has_var(INPUT_POST, "send_code")) {
    if ($emailServiceController->generateAndSendVerificationCode($_SESSION['email'])) {
        $_SESSION["_auth_change_password__success"] = "Verification Code Sent: The verification code was sent successfully...";
    } else {
        $_SESSION["_auth_change_password__error"] = "Verification Code Failed: The verification code could not be sent, please check for your internet connection and try again ...";
    }
}

?>


<body>
    <style>
        #verificationCodeInput::placeholder {
            font-size: 15px !important;
        }
    </style>
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
                                            <h5 class="fs-3xl">Verification Code</h5>
                                            <p class="text-muted mb-4">Enter the correct 6 verification code inorder to proceed to he password changing page</p>
                                        </div>

                                        <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                            Enter Verification Code here to proceed
                                        </div>
                                        <div class="p-2">
                                            <form action="" method='POST'>
                                                <div class="mb-1">
                                                    <label class="form-label">Verification Code</label>
                                                    <input type="number" name="verificationCode" class="form-control text-center password-input" placeholder="Enter Your 6 Verification Code here ..." id="verificationCodeInput" value="<?= $userController->handleNull("verificationCode", $userController->user) ?>" maxlength="6" oninput="applyMask(this)" style="font-size: 23px;">
                                                    <div class="text-danger my-2">
                                                        <?= $userController->handleNull("verificationCode", $userController->fieldErrors) ?>
                                                    </div>
                                                </div>
                                                <div class="text-danger my-1">
                                                    <?= $genError ?>
                                                </div>
                                                <div class="text-center mt-4">
                                                    <input type="submit" class="btn btn-success w-100" name="confirm_code" />
                                                </div>
                                            </form><!-- end form -->
                                            <div>
                                                <form action="" class="my-2" method="post">
                                                    <input type="submit" value="Resend Code" name="send_code" class="btn btn-light w-100 my-1">
                                                </form>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <p class="mb-0">Wait, I remember my password, <a href="../signin" class="fw-semibold text-primary text-decoration-underline"> Click here</a></p>
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


    <script>
        function applyMask(input) {
            // Remove any non-digit characters
            input.value = input.value.replace(/\D/g, '');

            // Ensure length is not more than 6
            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
        }
    </script>

    <?php include_once "../../includes/footer.php"; ?>