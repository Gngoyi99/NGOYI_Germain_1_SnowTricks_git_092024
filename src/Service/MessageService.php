<?php
namespace App\Service;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveMessage(Message $message): void
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function deleteMessage(Message $message): void
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }
}

