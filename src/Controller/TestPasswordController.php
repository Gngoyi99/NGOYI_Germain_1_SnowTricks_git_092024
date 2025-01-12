<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\VarDumper\VarDumper;

class TestPasswordController extends AbstractController
{
    #[Route('/test-password', name: 'test_password')]
    public function testPassword(UserPasswordHasherInterface $passwordHasher): void
    {
        $encodedPassword = '$2y$13$rrG9IqJbxU4JJ7FrXU49M.kgZEnmwMCurMe7e54Ojr2Oiw1eGiE/W'; // Le hash stocké en base
        $plainPassword = 'Test1234!'; // Le mot de passe saisi
    
        $user = new User();
        $isValid = $passwordHasher->isPasswordValid($user->setPassword($encodedPassword), $plainPassword);
    
        dd($isValid ? 'Valid password' : 'Invalid password');
    }
}
