<?php

namespace App\Repository;

use App\Entity\NotificationTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTemplate[]    findAll()
 * @method NotificationTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplate::class);
    }

    // /**
    //  * @return NotificationTemplate[] Returns an array of NotificationTemplate objects
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
    public function findOneBySomeField($value): ?NotificationTemplate
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
