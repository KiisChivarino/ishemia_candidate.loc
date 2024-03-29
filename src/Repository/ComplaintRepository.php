<?php

namespace App\Repository;

use App\Entity\Complaint;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Complaint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Complaint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Complaint[]    findAll()
 * @method Complaint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComplaintRepository extends AppRepository
{
    /**
     * ComplaintRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complaint::class);
    }

    /**
     * Returns complaints
     *
     * @param string $value
     *
     * @return array
     */
    public function findComplaints(string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('LOWER(c.name) LIKE LOWER(:valName)')
            ->andWhere('c.enabled = :valEnabled')
            ->setParameter('valName', '%'.$value.'%')
            ->setParameter('valEnabled', true)
            ->orderBy('c.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
