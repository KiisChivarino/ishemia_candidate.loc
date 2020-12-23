<?php

namespace App\Repository;

use App\Entity\PatientNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientNotification[]    findAll()
 * @method PatientNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientNotification::class);
    }

    // /**
    //  * @return PatientNotification[] Returns an array of PatientNotification objects
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
    public function findOneBySomeField($value): ?PatientNotification
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
