<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config;

class Mail {

    public static function send($to, $subject, $text, $html) {
        $mail = new PHPMailer(true);

        try {
            //Server settings 
            //$mail->SMTPDebug  = 4;                                    //Enable verbose debug output
            $mail->isSMTP();            
            $mail->Host       = Config::SMTP;                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = Config::MAIL_ADDRESS;                   //SMTP username
            $mail->Password   = Config::PASSWORD;                 //SMTP password
            $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom(Config::MAIL_ADDRESS, 'Mailer');
            $mail->addAddress($to);                                     //Add a recipient

            //Content
            $mail->isHTML(true);                                        //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;
        
            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}