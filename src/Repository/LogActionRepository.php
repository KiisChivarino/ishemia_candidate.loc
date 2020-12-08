<?php

namespace App\Repository;

use App\Entity\Logger\LogAction;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAction[]    findAll()
 * @method LogAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogActionRepository extends AppRepository
{
    /**
     * LogActionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAction::class);
    }
}
