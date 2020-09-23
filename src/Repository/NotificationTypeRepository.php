<?php

namespace App\Repository;

use App\Entity\NotificationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class NotificationTypeRepository
 * @method NotificationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationType[]    findAll()
 * @method NotificationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class NotificationTypeRepository extends ServiceEntityRepository
{
    /**
     * NotificationTypeRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationType::class);
    }
}
