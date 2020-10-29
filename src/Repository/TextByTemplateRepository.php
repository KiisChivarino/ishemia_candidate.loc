<?php

namespace App\Repository;

use App\Entity\TextByTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TextByTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextByTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextByTemplate[]    findAll()
 * @method TextByTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextByTemplateRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextByTemplate::class);
    }

    // /**
    //  * @return TextByTemplate[] Returns an array of TextByTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TextByTemplate
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
