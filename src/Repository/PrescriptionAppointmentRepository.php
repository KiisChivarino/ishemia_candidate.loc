<?php

namespace App\Repository;

use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PrescriptionAppointmentRepository
 * @method PrescriptionAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionAppointment[]    findAll()
 * @method PrescriptionAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PrescriptionAppointmentRepository extends AppRepository
{
    /**
     * PrescriptionAppointmentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionAppointment::class);
    }

    /**
     * Returns count of appointments
     * @param Prescription $prescription
     * @return int
     */
    public function countPrescriptionAppointmentsByPrescription(Prescription $prescription): int
    {
        return $this->count(
            [
                'prescription' => $prescription,
                'enabled' => true,
            ]
        );
    }
}
