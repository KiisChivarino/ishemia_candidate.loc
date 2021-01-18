<?php

namespace App\Repository;

use App\Entity\PatientNotification;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientNotificationRepository
 * @method PatientNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientNotification[]    findAll()
 * @method PatientNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PatientNotificationRepository extends AppRepository
{
    /**
     * PatientNotificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientNotification::class);
    }
}
