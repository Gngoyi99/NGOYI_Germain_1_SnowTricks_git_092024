<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class ArticleVoter extends Voter
{
    public const EDIT = 'article_edit';
    public const DELETE = 'article_delete';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        /** @var Article $article */
        $article = $subject;

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }

                return $article->getUserId() === $user;
        }

        return false;
    }
}
