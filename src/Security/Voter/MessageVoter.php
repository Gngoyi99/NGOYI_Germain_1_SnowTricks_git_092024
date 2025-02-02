<?php

namespace App\Security\Voter;

use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class MessageVoter extends Voter
{
    public const EDIT = 'message_edit';
    public const DELETE = 'message_delete';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Message;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        /** @var Message $message */
        $message = $subject;

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                // Admins ont toujours accès
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }

                // L'utilisateur doit être l'auteur
                return $message->getUser() === $user;
        }

        return false;
    }
}
