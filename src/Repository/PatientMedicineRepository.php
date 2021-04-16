<?php

namespace App\Repository;

use App\Entity\PatientMedicine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientMedicineRepository
 *
 * @method PatientMedicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientMedicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientMedicine[]    findAll()
 * @method PatientMedicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientMedicineRepository extends ServiceEntityRepository
{
    /**
     * PatientMedicineRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientMedicine::class);
    }
}
