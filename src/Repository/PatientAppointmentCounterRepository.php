<?php

namespace App\Repository;

use App\Entity\PatientAppointment;

/**
 * Class PatientAppointmentCounterRepository
 *
 * @method PatientAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAppointment[]    findAll()
 * @method PatientAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientAppointmentCounterRepository extends PatientAppointmentDataTableRepository
{
    /**
     * Count not processed patient appointment
     *
     * @param $patientId
     *
     * @return int
     */
    public function NotProcessedPatientAppointmentCounter($patientId): int
    {
        return $this->countByPatientAppointmentId(
            parent::getNoProcessedPatientAppointments(
                parent::generateStandardJoinsAndWheres(
                    $this->createQueryBuilder('paT'),
                    $patientId
                )
            )
        );
    }

    /**
     * Add count by patient appointment id
     *
     * @param $qb
     *
     * @return int
     */
    private function countByPatientAppointmentId($qb): int
    {
        return sizeof($qb->select('paT.id')
            ->getQuery()
            ->getResult());
    }
}
