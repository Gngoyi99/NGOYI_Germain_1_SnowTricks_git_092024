<?php
// src/Controller/EmailTestController.php

namespace App\Controller;

use App\Service\Mail;  // Import du service Mail
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class EmailTestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function sendTestEmail(Mail $mailService, LoggerInterface $logger): Response
    {
        $logger->info('Starting to send email');

        // Données d'email
        $fromEmail = 'test@example.com';
        $fromName = 'Test Sender';
        $toEmail = 'user@example.com';
        $subject = 'Test Email';
        $body = 'This is a test email sent using PHPMailer in Symfony.';

        // Envoi de l'email via PHPMailer
        $result = $mailService->sendEmail($fromEmail, $fromName, $toEmail, $subject, $body);

        if (strpos($result, 'successfully') !== false) {
            $logger->info('Email sent successfully');
            return new Response('Email sent successfully');
        } else {
            $logger->error('Failed to send email: '.$result);
            return new Response('Failed to send email: '.$result, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
