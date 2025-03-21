<?php

namespace App\controllers;

use App\core\Controller;
use App\helpers\EmailServiceHelper;

class EmailServiceController extends Controller
{
    # Some logic here ..
    public EmailServiceHelper $emailServiceHelper;

    public function __construct()
    {
        $this->emailServiceHelper = new EmailServiceHelper;
    }

    public function generateAndSendVerificationCode($recipients_email)
    {
        $verificationCode = (new AuthUserController)->makeVerificationCode();

        $_SESSION['verificationCode'] = $verificationCode;
        $_SESSION['timeGenerated'] = time();
        $_SESSION['email'] = $recipients_email;

        $errors = "";

        $currentDate = $this->format_date(date("Y-m-d"));

        $body = <<<"HTML"
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Montserrat', sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    #email-template {
                        overflow: hidden;
                        max-width: 600px;
                        margin: 20px auto;
                        border-radius: 8px;
                        background-color: #fff;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    #email-template header {
                        background-color: #4CAF50;
                        color: #ffffff;
                        padding: 10px 20px;
                        border-top-left-radius: 8px;
                        border-top-right-radius: 8px;
                    }
                    #email-template header h2 {
                        margin: 0;
                        color: #fff; 
                        font-weight: 500;
                    }
                    #email-template div {
                        padding: 20px;
                        text-align: center;
                    }
                    #email-template img {
                        margin: 15px 0;
                        max-width: 100px;
                    }
                    #email-template h2 {
                        color: #444;
                        font-weight: 700;
                    }
                    #email-template h5 {
                        color: #444;
                        margin: 0;
                        font-size: 16px;
                        font-weight: 400;
                    }
                    #verification-code {
                        font-size: 28px;
                        color: #444;
                        padding: 10px 20px; 
                        border-radius: 10px; 
                        background: #eee; 
                        margin: 10px 0 0 0;
                        display: inline-block;
                        font-weight: 700;
                    }
                    .date {
                        font-size: 14px;
                        color: #888888;
                        margin-top: 10px;
                        font-weight: 400;
                    }
                    footer {
                        background-color: #f4f4f4;
                        color: #888888;
                        padding: 10px;
                        text-align: center;
                        font-size: 12px;
                        border-bottom-left-radius: 8px;
                        border-bottom-right-radius: 8px;
                        font-weight: 400;
                    }
                </style>
            </head>
            <body>
                <div id="email-template">
                    <header>
                        <h2>Islamic Call University College</h2>
                    </header>
                    <div>
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRP3s5B7DDIDj_rO50NZX087VyThO_69jB-gi8tX3Nx-ktzC3Dl31aw_aPdarsQQiUBCVI&usqp=CAU" alt="icuc_logo" />
                        <h5>Your 6 Digit Verification Code</h5>
                        <h2 id="verification-code">$verificationCode</h2>
                        <div class="date">Date: $currentDate</div>
                    </div>
                    <footer>
                        <p>&copy; 2024 Islamic Call University College. All rights reserved.</p>
                        <p>This is an automated message, please do not reply.</p>
                    </footer>
                </div>
            </body>
            </html>

        HTML;

        return $this->emailServiceHelper->sendEmail(email: $recipients_email, subject: "ICUC Password Change Verification Code", body: $body);
    }
}
