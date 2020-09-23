<?php

namespace App\Repository;

use App\Entity\Analysis;
use App\Entity\AnalysisRate;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalysisRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalysisRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalysisRate[]    findAll()
 * @method AnalysisRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalysisRateRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalysisRate::class);
    }

    /**
     * Возвращает самый поздний минимальный срок гестации среди включенных референтных значений анализа
     *
     * @param Analysis $analysis
     *
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function getMaxGestationMinAge(Analysis $analysis)
//    {
//        return $this->createQueryBuilder('a')
//            ->select('MAX(p.gestationMinAge)')
//            ->leftJoin('a.period', 'p')
//            ->andWhere('a.enabled = true and a.analysis = :val')
//            ->setParameter('val', $analysis)
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

    /**
     * Возвращает самый ранний максимальный срок гестации среди включенных референтных значений анализа
     *
     * @param Analysis $analysis
     *
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function getMinGestationMaxAge(Analysis $analysis)
//    {
//        return $this->createQueryBuilder('a')
//            ->select('MIN(p.gestationMaxAge)')
//            ->leftJoin('a.period', 'p')
//            ->andWhere('a.enabled = true and a.analysis = :val')
//            ->setParameter('val', $analysis)
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

    /**
     * Возвращает самый поздний максимальный срок гестации среди включенных референтных значений анализа
     *
     * @param Analysis $analysis
     *
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function getMaxGestationMaxAge(Analysis $analysis)
//    {
//        return $this->createQueryBuilder('a')
//            ->select('MAX(p.gestationMaxAge)')
//            ->leftJoin('a.period', 'p')
//            ->andWhere('a.enabled = true and a.analysis = :val')
//            ->setParameter('val', $analysis)
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

    /**
     * Возвращает самый ранний минимальный срок гестации среди включенных референтных значений анализа
     *
     * @param Analysis $analysis
     *
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function getMinGestationMinAge(Analysis $analysis)
//    {
//        return $this->createQueryBuilder('a')
//            ->select('MIN(p.gestationMinAge)')
//            ->leftJoin('a.period', 'p')
//            ->andWhere('a.enabled = true and a.analysis = :val')
//            ->setParameter('val', $analysis)
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

    // /**
    //  * @return AnalysisRate[] Returns an array of AnalysisRate objects
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
    public function findOneBySomeField($value): ?AnalysisRate
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
