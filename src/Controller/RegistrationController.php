<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private $mailService;

    public function __construct(Mail $mailService)
    {
        $this->mailService = $mailService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            ));

            // Définition du rôle et du nom d'utilisateur
            $user->setRoles(['ROLE_USER']);
            $user->setUsername($form->get('username')->getData());

            // Générer un token pour la vérification du mail
            $token = bin2hex(random_bytes(16));
            $user->setVerificationToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            // Création du lien de validation
            $validationUrl = $this->generateUrl('app_verify_email', ['token' => $token], 0);

            // Envoi de l'email de confirmation
            $subject = "Confirmez votre email";
            $body = "<p>Bonjour {$user->getUsername()},</p>
                    <p>Veuillez cliquer sur le lien suivant pour valider votre compte : 
                    <a href='{$validationUrl}'>Valider mon compte</a></p>";

            if ($this->mailService->sendEmail(
                'no-reply@your-domain.com', 
                'Service d\'inscription', 
                $user->getEmail(), 
                $subject, 
                $body
            )) {
                // Message de succès
                $this->addFlash('success', 'Un email de confirmation vous a été envoyé. Veuillez vérifier votre boîte de réception.');
            } else {
                // Message d'erreur
                $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(string $token, UserRepository $userRepository, TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            // Message d'erreur
            $this->addFlash('danger', 'Le lien de validation est invalide ou a déjà été utilisé.');
            return $this->redirectToRoute('app_register');
        }

        $user->setVerified(true);
        $user->setVerificationToken(null); // Passer le token à null
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', 'Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }
}
