<?php

namespace App\Repository;

use App\Entity\Medicine;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class MedicineRepository
 * * @method Medicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicine[]    findAll()
 * @method Medicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class MedicineRepository extends AppRepository
{
    /**
     * MedicineRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicine::class);
    }

    /**
     * @param string|null $value
     *
     * @return array
     */
    public function findMedicines(?string $value): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('LOWER(m.name) LIKE LOWER(:valName)')
            ->andWhere('m.enabled = :valEnabled')
            ->setParameter('valEnabled', true)
            ->setParameter('valName', '%'.$value.'%')
            ->orderBy('m.name', 'ASC')
            ->setMaxResults(10)->getQuery()->getResult();
    }
}
