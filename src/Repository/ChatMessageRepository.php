<?php

namespace App\Repository;

use App\Entity\ChatMessage;
use App\Entity\User;
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

    public function getLastMessages(ChatConversation $conv)
    {
        return $this->createQueryBuilder('cm')
            ->where("cm.conversation = :conv")
            ->setParameter("conv", $conv)
            ->addOrderBy('cm.id', 'DESC')
            ->addOrderBy('cm.submit_time', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLastMessagesFromOther(ChatConversation $conv, User $user)
    {
        return $this->createQueryBuilder('cm')
            ->where("cm.conversation = :conv")
            ->andWhere("cm.submit_time > :date")
            ->andWhere("cm.sender != :user")
            ->setParameter("conv", $conv)
            ->setParameter('date', new \DateTime('-5 seconds',
                new \DateTimeZone('Europe/Paris')))
            ->setParameter("user", $user)
            ->addOrderBy('cm.id', 'DESC')
            ->addOrderBy('cm.submit_time', 'DESC')
            ->getQuery()
            ->getResult();
    }
}