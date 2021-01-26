<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $text, $html)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        //Server settings
        $mail->isSMTP();
        $mail->Host = Config::MAIL_HOST;
        $mail->SMTPAuth = Config::MAIL_HOST_AUTHENTICATION;
        $mail->Username = Config::MAIL_USERNAME;
        $mail->Password = Config::MAIL_PASSWORD;
        $mail->SMTPSecure = Config::MAIL_SMTP_SECURE_TYPE;
        $mail->Port = Config::MAIL_SMTP_PORT;

        $domain = Config::MAILGUN_DOMAIN;

        $mail->setFrom(Config::MAIL_USERNAME, 'Mailer');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = $text;

        $mail->send();
    }
}
