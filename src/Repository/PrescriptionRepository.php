<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prescription[]    findAll()
 * @method Prescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionRepository extends AppRepository
{
    /**
     * PrescriptionRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    /**
     * Find not completed prescription
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return Prescription|null
     */
    public function findNotCompletedPrescription(MedicalHistory $medicalHistory): ?Prescription
    {
        return $this->findOneBy(
            [
                'isCompleted' => false,
                'medicalHistory' => $medicalHistory
            ]
        );
    }
}
