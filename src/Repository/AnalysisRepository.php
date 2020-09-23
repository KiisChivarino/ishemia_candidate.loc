<?php

namespace App\Repository;

use App\Entity\Analysis;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Analysis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analysis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analysis[]    findAll()
 * @method Analysis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalysisRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analysis::class);
    }
}
