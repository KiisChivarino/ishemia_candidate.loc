<?php

namespace App\Repository;

use App\Entity\NotificationConfirm;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationConfirm|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationConfirm|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationConfirm[]    findAll()
 * @method NotificationConfirm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationConfirmRepository extends AppRepository
{
    /**
     * NotificationConfirmRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationConfirm::class);
    }
}
