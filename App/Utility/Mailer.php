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
     * @param string $article_title
     * @param string $article_image_url
     * @param string $message_content
     * @param string $article_url
     * @return void
     *
     * @throws MailerException
     */
    public static function send(string $fromName, string $fromMail, string $toName, string $toMail, string $article_title, string $article_image_url, string $message_content, string $article_url): void
    {
        $mail = new PHPMailer(true);
        $mailTemplate = file_get_contents(__DIR__ . '/../MailTemplate/message_mail.html');

        $mailTemplate = str_replace('{{article_title}}', $article_title, $mailTemplate);
        $mailTemplate = str_replace('{{image_link}}', $article_image_url, $mailTemplate);
        $mailTemplate = str_replace('{{message_content}}', $message_content, $mailTemplate);
        $mailTemplate = str_replace('{{message_author}}', $fromName, $mailTemplate);
        $mailTemplate = str_replace('{{article_link}}', $article_url, $mailTemplate);
        $mailTemplate = str_replace('{{mailto}}', $fromMail, $mailTemplate);


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
            $mail->Subject = "[VGEL] Vous avez reçu un message de " . $fromName;
            $mail->Body = $mailTemplate;
            $mail->AltBody = $mailTemplate;
            $mail->CharSet = "UTF-8";

            $mail->send();
        } catch (Exception|PHPMailerException $e) {
            throw new MailerException("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
        }
    }
}