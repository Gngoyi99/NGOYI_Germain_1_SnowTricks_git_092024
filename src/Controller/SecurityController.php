<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
        
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, Mail $mailService, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Recherche de l'utilisateur par email
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Génération d'un nouveau token
                $token = bin2hex(random_bytes(16));
                $user->setVerificationToken($token);
                $entityManager->flush();

                // Envoi du mail avec le nouveau token
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], 0);
                $subject = "Réinitialisation de votre mot de passe";
                $body = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : <a href='{$resetUrl}'>Réinitialiser mon mot de passe</a>";
                $mailService->sendEmail('no-reply@your-domain.com', 'Support', $email, $subject, $body);
            }

            // Message générique pour ne pas dire si l'utilisateur existe ou non
            $this->addFlash('success', 'Si cet email est enregistré, un lien de réinitialisation vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig');
    }


    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager): Response {
        // Recherche de l'utilisateur avec le token
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');

            if (!empty($newPassword)) {
                // Mise à jour du mot de passe
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

                // Invalider le token après utilisation
                $user->setVerificationToken(null);
                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }

            // Message d'erreur
            $this->addFlash('error', 'Le mot de passe ne peut pas être vide.');
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }



}
