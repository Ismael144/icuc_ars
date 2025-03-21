<?php

namespace App\helpers;

use App\core\Helper;
use PHPMailer\PHPMailer\PHPMailer;

class EmailServiceHelper extends Helper
{
    public function __construct()
    {
    }

    /**
     * Sends html mime type to gmail  
     * @param string $email
     * @param string $subject
     * @param string $body
     * @param string|null $atchment_path 
     * 
     * @todo You need to initialize the $errorCollection array
     * 
     * @return string|bool
     * 
     */
    public function sendEmail($email, $subject, $body, $atchment_path = null): string|bool
    {
        # Gets the $errorCollection array initialized outside the method
        global $errors;

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'swalehismael144@gmail.com';
            $mail->Password = 'bwxsdsmqlgwvwsqp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('swalehismael144@gmail.com');
            $mail->addAddress($email);
            $mail->isHTML(true);

            if ($atchment_path != null) {
                $mail->addAttachment($atchment_path);
            }

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();

            return true;
        } catch (\Exception $e) {
            $errors = "Sorry, An Error Occured While Attempting to Reach The Servers, Check Your Internet Connection And Try Again.";
            return false;
        }
    }
}
