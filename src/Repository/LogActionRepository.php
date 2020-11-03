<?php

namespace App\Repository;

use App\Entity\Logger\LogAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAction[]    findAll()
 * @method LogAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogActionRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAction::class);
    }


    // /**
    //  * @return LogAction[] Returns an array of LogAction objects
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
    public function findOneBySomeField($value): ?LogAction
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
