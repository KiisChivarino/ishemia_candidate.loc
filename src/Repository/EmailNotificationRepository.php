<?php

namespace App\Repository;

use App\Entity\EmailNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailNotification[]    findAll()
 * @method EmailNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailNotification::class);
    }

    // /**
    //  * @return EmailNotification[] Returns an array of EmailNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmailNotification
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
