<?php

namespace App\Services\DataTable\DoctorOffice\PatientAppointment;

use Doctrine\ORM\QueryBuilder;

/**
 * Class PatientAppointmentNoProcessedDataTable
 *
 * @package App\Services\DataTable\DoctorOffice\PatientAppointment
 */
class PatientAppointmentNoProcessedDataTable extends PatientAppointmentDatatable
{
    /**
     * @param QueryBuilder $builder
     * @param int $patientId
     *
     * @return QueryBuilder
     */
    protected function getAppointments(QueryBuilder $builder, int $patientId): QueryBuilder
    {
        return $this->patientAppointmentDatatableRepository
            ->getNoProcessedAppointmentsForPatientForDataTable($builder, $patientId);
    }
}
