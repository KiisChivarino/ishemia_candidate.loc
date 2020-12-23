<?php

namespace App\Repository;

use App\Entity\WebNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WebNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebNotification[]    findAll()
 * @method WebNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebNotification::class);
    }

    // /**
    //  * @return WebNotification[] Returns an array of WebNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebNotification
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
