<?php

declare(strict_types=1);

namespace App\Utility;

use App\Exception\MailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Exception;

class Mailer
{
    /**
     * Envoyer un email
     *
     * @param string $fromName
     * @param string $fromMail
     * @param string $toName
     * @param string $toMail
     * @param string $subject
     * @param string $content
     *
     * @return void
     *
     * @throws MailerException
     */
    public static function send(string $fromName, string $fromMail, string $toName, string $toMail, string $subject, string $content): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.us.appsuite.cloud';
            $mail->SMTPAuth = true;
            $mail->Username = 'peritia@cyber-dodo.fr';
            $mail->Password = 'testtoto1234';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('peritia@cyber-dodo.fr', 'Peritia');
            $mail->addAddress($toMail, $toName);
            $mail->addReplyTo($fromMail, $fromName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = '<div style="background-color: #f8f8f8; padding: 10px; text-align: left;">' . $content . '</div>';
            $mail->AltBody = $content;

            $mail->send();
        } catch (Exception|PHPMailerException $e) {
            throw new MailerException("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
        }
    }
}