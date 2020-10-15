<?php

namespace App\Repository;

use App\Entity\TimeRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeRange|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeRange|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeRange[]    findAll()
 * @method TimeRange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeRangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeRange::class);
    }

    // /**
    //  * @return TimeRange[] Returns an array of TimeRange objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeRange
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
