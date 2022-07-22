<?php

namespace App\Repository;

use App\Entity\PatientAppointment;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PatientAppointmentDataTableRepository
 *
 * @method PatientAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAppointment[]    findAll()
 * @method PatientAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientAppointmentDataTableRepository extends PatientAppointmentRepository
{
    /**
     * Generates Query Builder For Datatable
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function generateQueryBuilderForDatatable(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->from(PatientAppointment::class, 'paT')
            ->select('paT');
    }

    /**
     * Gets no processed patient appointment for Datatable
     *
     * @param QueryBuilder $queryBuilder
     * @param $patientId
     *
     * @return QueryBuilder
     */
    public function getNoProcessedAppointmentsForPatientForDataTable(QueryBuilder $queryBuilder, $patientId): QueryBuilder
    {
        return
            parent::getNoProcessedPatientAppointments(
                parent::generateStandardJoinsAndWheres(
                    $this->generateQueryBuilderForDatatable($queryBuilder),
                    $patientId
                )
            );
    }

    /**
     * Generate Is Missed Query Builder For Patient Appointment
     *
     * @param QueryBuilder $queryBuilder
     * @param $patientId
     *
     * @return QueryBuilder
     */
    public function getHistoryAppointmentsForPatientForDataTable(QueryBuilder $queryBuilder, $patientId): QueryBuilder
    {
        return parent::getHistoryPatientAppointments(
            parent::generateStandardJoinsAndWheres(
                $this->generateQueryBuilderForDatatable($queryBuilder),
                $patientId
            )
        );
    }
}
