<?php

namespace App\Repository;

use App\Entity\ChannelType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChannelType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChannelType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChannelType[]    findAll()
 * @method ChannelType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChannelType::class);
    }

    // /**
    //  * @return ChannelType[] Returns an array of ChannelType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChannelType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
