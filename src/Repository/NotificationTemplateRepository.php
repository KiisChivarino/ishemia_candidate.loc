<?php

namespace App\Repository;

use App\Entity\NotificationTemplate;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTemplate[]    findAll()
 * @method NotificationTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTemplateRepository extends AppRepository
{
    /**
     * NotificationTemplateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplate::class);
    }
}
