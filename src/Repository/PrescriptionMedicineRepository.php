<?php

namespace App\Repository;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PrescriptionMedicineRepository
 * @method PrescriptionMedicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionMedicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionMedicine[]    findAll()
 * @method PrescriptionMedicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PrescriptionMedicineRepository extends AppRepository
{
    /**
     * PrescriptionMedicineRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionMedicine::class);
    }

    /**
     * Counts prescription medicines by prescription
     * @param Prescription $prescription
     * @return int
     */
    public function countPrescriptionMedicinesByPrescription(
        Prescription $prescription
    ): int
    {
        return $this->count(['prescription' => $prescription, 'enabled' => true]);
    }
}
