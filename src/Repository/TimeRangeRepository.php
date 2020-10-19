<?php

namespace App\Repository;

use App\Entity\TimeRange;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeRange|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeRange|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeRange[]    findAll()
 * @method TimeRange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeRangeRepository extends AppRepository
{
    /**
     * TimeRangeRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeRange::class);
    }
}
