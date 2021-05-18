<?php

namespace App\Repository;

use App\Entity\WebNotification;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class WebNotificationRepository
 * @method WebNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebNotification[]    findAll()
 * @method WebNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class WebNotificationRepository extends AppRepository
{
    /**
     * WebNotificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebNotification::class);
    }
}
