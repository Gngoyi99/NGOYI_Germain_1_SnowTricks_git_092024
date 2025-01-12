<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Mail; // Import du service PHPMailer
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

    public function __construct(Mail $mailService) // Injection du service PHPMailer
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
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encodage du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Définition du rôle par défaut
            $user->setRoles(['ROLE_USER']);

            // Définition du nom d'utilisateur
            $user->setUsername($form->get('username')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // Générer un token de validation
            $token = bin2hex(random_bytes(16));
            $user->setVerificationToken($token);
            $entityManager->flush();

            // Créer le lien de validation
            $validationUrl = $this->generateUrl('app_verify_email', ['token' => $token], 0); // 0 pour obtenir une URL absolue

            // Envoi de l'email via PHPMailer
            $subject = "Confirmez votre email";
            $body = "<p>Bonjour {$user->getUsername()},</p>
                    <p>Veuillez cliquer sur le lien suivant pour valider votre compte : 
                    <a href='{$validationUrl}'>Valider mon compte</a></p>";

            $result = $this->mailService->sendEmail(
                'no-reply@your-domain.com', 
                'Service d\'inscription', 
                $user->getEmail(), 
                $subject, 
                $body
            );

            // Redirection après l'envoi de l'email
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(string $token, UserRepository $userRepository, TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
    {
        // Recherche de l'utilisateur en fonction du token
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (null === $user) {
            // Utilisateur non trouvé ou token invalide
            $this->addFlash('verify_email_error', $translator->trans('Le lien de validation est invalide.'));
            return $this->redirectToRoute('app_register');
        }

        // Validation du compte
        $user->setVerified(true);
        $user->setVerificationToken(null);  // Supprimer le token de validation
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été activé avec succès.');

        // Redirection vers la page de connexion ou accueil
        return $this->redirectToRoute('app_login');
    }
}
