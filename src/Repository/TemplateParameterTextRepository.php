<?php

namespace App\Repository;

use App\Entity\TemplateParameterText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateParameterText|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameterText|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameterText[]    findAll()
 * @method TemplateParameterText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateParameterTextRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameterText::class);
    }

    // /**
    //  * @return TemplateParameterText[] Returns an array of TemplateParameterText objects
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
    public function findOneBySomeField($value): ?TemplateParameterText
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
