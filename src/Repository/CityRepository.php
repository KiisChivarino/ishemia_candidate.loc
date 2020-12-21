<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends AppRepository
{
    /**
     * CityRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * Find cities for ajax
     *
     * @param string $value
     *
     * @return array
     */
    public function findCities(string $value): array
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
