<?php

namespace App\Repository;

use App\Entity\AnalysisGroup;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalysisGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalysisGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalysisGroup[]    findAll()
 * @method AnalysisGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalysisGroupRepository extends AppRepository
{
    /**
     * AnalysisGroupRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalysisGroup::class);
    }

    /**
     * Find analysis groups for ajax
     * @param string|null $value
     * @return array
     */
    public function findAnalysisGroups(?string $value): array
    {
        return $this->createQueryBuilder('ag')
            ->andWhere('LOWER(ag.name) LIKE LOWER(:valName) or LOWER(ag.fullName) LIKE LOWER(:valName)')
            ->andWhere('ag.enabled = :valEnabled')
            ->setParameter('valEnabled', true)
            ->setParameter('valName', '%' . $value . '%')
            ->orderBy('ag.name', 'ASC')
            ->setMaxResults(10)->getQuery()->getResult();
    }
}
