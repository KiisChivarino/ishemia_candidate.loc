<?php

namespace App\Repository;

use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PrescriptionTestingRepository
 * @method PrescriptionTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionTesting[]    findAll()
 * @method PrescriptionTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PrescriptionTestingRepository extends AppRepository
{
    /**
     * PrescriptionTestingRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionTesting::class);
    }

    /**
     * Counts enabled testings by prescription
     * @param Prescription $prescription
     * @return int
     */
    public function countPrescriptionTestingsByPrescription(Prescription $prescription): int
    {
        return $this->count(
            [
                'prescription' => $prescription, 'enabled' => true
            ]
        );
    }
}
