<?php

namespace App\Repository;

use App\Entity\PatientFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientFile[]    findAll()
 * @method PatientFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientFile::class);
    }

    // /**
    //  * @return PatientFile[] Returns an array of PatientFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PatientFile
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
