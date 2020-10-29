<?php

namespace App\Repository;

use App\Entity\TemplateParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameter[]    findAll()
 * @method TemplateParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateParameterRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameter::class);
    }

    public function findById($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
//            ->leftJoin('t.templateParameterTexts', 'templateParameterTexts')
//            ->addSelect('templateParameterTexts')
//            ->leftJoin('t.templateType', 'templateType')
//            ->addSelect('templateType')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return TemplateParameter[] Returns an array of TemplateParameter objects
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
    public function findOneBySomeField($value): ?TemplateParameter
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
