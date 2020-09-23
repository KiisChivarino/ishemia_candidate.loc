<?php

namespace App\Repository;

use App\Entity\AnalysisGroup;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalysisGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalysisGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalysisGroup[]    findAll()
 * @method AnalysisGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalysisGroupRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalysisGroup::class);
    }

    // /**
    //  * @return AnalysisGroup[] Returns an array of AnalysisGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnalysisGroup
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
