<?php
// src/Service/Mail.php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public function sendEmail(string $fromEmail, string $fromName, string $toEmail, string $subject, string $body): string
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = '127.0.0.1';  // Serveur SMTP (MailHog ou votre serveur SMTP)
            $mail->SMTPAuth = false;    // Si pas d'authentification
            $mail->Port = 1025;         // Port SMTP

            // Expéditeur
            $mail->setFrom($fromEmail, $fromName);

            // Destinataire
            $mail->addAddress($toEmail);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            // Envoi de l'email
            $mail->send();
            return 'Email envoyé avec succès';
        } catch (Exception $e) {
            return "Échec de l'envoi de l'email. Erreur Mailer : {$mail->ErrorInfo}";
        }
    }
}
