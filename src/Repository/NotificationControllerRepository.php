<?php

namespace App\Repository;

use App\Entity\NotificationController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationController|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationController|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationController[]    findAll()
 * @method NotificationController[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationControllerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationController::class);
    }

    // /**
    //  * @return NotificationController[] Returns an array of NotificationController objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotificationController
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
