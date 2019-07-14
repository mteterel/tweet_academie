<?php

namespace App\Repository;

use App\Entity\ChatMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\ChatConversation;

/**
 * @method ChatMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMessage[]    findAll()
 * @method ChatMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

    // /**
    //  * @return ChatMessage[] Returns an array of ChatMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChatMessage
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getLastMessages(ChatConversation $conv)
    {
        return $this->createQueryBuilder('cm')
                ->where("cm.conversation = :conv")
                ->setParameter("conv", $conv)
                ->orderBy("cm.submit_time", "DESC")
                ->getQuery()
                ->getResult();
    }

    public function getLastMessagesFromOther(ChatConversation $conv, $user_id)
    {
        return $this->createQueryBuilder('cm')
                ->where("cm.conversation = :conv")
                ->andWhere("cm.submit_time > :date")
                ->andWhere("cm.sender != $user_id")
                ->setParameter("conv", $conv)
                ->setParameter('date', new \DateTime('-5 seconds',
                new \DateTimeZone('Europe/Paris')))
                ->orderBy("cm.submit_time", "DESC")
                ->getQuery()
                ->getResult();
    }
}