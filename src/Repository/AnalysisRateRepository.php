<?php

namespace App\Repository;

use App\Entity\AnalysisRate;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AnalysisRateRepository
 * @method AnalysisRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalysisRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalysisRate[]    findAll()
 * @method AnalysisRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class AnalysisRateRepository extends AppRepository
{
    /**
     * AnalysisRateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalysisRate::class);
    }
}
