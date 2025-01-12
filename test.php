<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$email = 'expediteur@example.com'; // Remplacez par votre email d'expéditeur
$name = 'Nom Expéditeur'; // Remplacez par votre nom d'expéditeur
$surname = 'Prénom Expéditeur'; // Remplacez par votre prénom d'expéditeur
$message = 'Ceci est un message de test.'; // Votre message

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = '127.0.0.1';
    $mail->SMTPAuth = false;
    $mail->Port = 1025;

    $mail->setFrom($email, $name);
    $mail->addAddress('destinataire@example.com'); // Remplacez par l'email du destinataire

    $mail->isHTML(true);
    $mail->Subject = 'Nouveau message de contact';
    $mail->Body    = "Nom: $surname<br>Prénom: $name<br>E-mail: $email<br>Message: $message";
    $mail->AltBody = "Nom: $surname\nPrénom: $name\nE-mail: $email\nMessage: $message";

    $mail->send();
    echo 'Votre message a été envoyé avec succès.';
} catch (Exception $e) {
    echo "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer plus tard. Mailer Error: {$mail->ErrorInfo}";
}


class Pont
{
    private string $unite = 'm²';

    private float $longueur;
    private float $largeur;

    public function setLongeur(float $longueur):void
    {
        if ($longueur < 0){
            trigger_error(
                'la longeur est trop courte',
                E_USER_ERROR
            );
        }
        $this->longueur = $longueur;
    }

}
$towerBridge = new Pont;
$towerBridge->setLongeur(45,2);