<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getMessagesPaginated(Article $article, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        // Construisez la requête
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.article = :article')
            ->setParameter('article', $article)
            ->orderBy('m.created_at', 'DESC')
            ->setFirstResult($offset) // OFFSET
            ->setMaxResults($limit) // LIMIT
            ->getQuery();

        // Exécutez la requête et récupérez les messages
        $messages = $query->getResult();


        // Comptez le nombre total de messages pour cet article
        $totalMessages = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'messages' => $messages,
            'totalPages' => ceil($totalMessages / $limit),
            'totalMessages' => $totalMessages,
        ];
    }


}
