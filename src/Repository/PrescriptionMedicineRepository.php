<?php

namespace App\Repository;

use App\Entity\PrescriptionMedicine;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrescriptionMedicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionMedicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionMedicine[]    findAll()
 * @method PrescriptionMedicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionMedicineRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionMedicine::class);
    }
}
