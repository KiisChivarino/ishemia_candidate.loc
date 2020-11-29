<?php

namespace App\Repository;

use App\Entity\TemplateParameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameter[]    findAll()
 * @method TemplateParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateParameterRepository extends AppRepository
{
    /**
     * TemplateParameterRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameter::class);
    }

    /**
     * @param $value
     * @return int|mixed|string
     */
    public function findById($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
