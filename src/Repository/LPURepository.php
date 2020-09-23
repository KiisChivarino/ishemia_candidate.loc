<?php

namespace App\Repository;

use App\Entity\LPU;
use App\Repository\AppRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LPU|null find($id, $lockMode = null, $lockVersion = null)
 * @method LPU|null findOneBy(array $criteria, array $orderBy = null)
 * @method LPU[]    findAll()
 * @method LPU[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LPURepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LPU::class);
    }

    public function getKurskRegionLPU(){
        return $this->findBy(['oktmoRegionId' => 38]);
    }
    // /**
    //  * @return LPU[] Returns an array of LPU objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LPU
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
