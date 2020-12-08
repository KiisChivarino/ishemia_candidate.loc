<?php

namespace App\Repository;

use App\Entity\SMSNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SMSNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMSNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMSNotification[]    findAll()
 * @method SMSNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMSNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SMSNotification::class);
    }
}
