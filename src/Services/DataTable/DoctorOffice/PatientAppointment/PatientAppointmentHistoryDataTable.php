<?php

namespace App\Services\DataTable\DoctorOffice\PatientAppointment;

use Doctrine\ORM\QueryBuilder;

/**
 * Class PatientAppointmentHistoryDataTable
 *
 * @package App\Services\DataTable\DoctorOffice\PatientAppointment
 */
class PatientAppointmentHistoryDataTable extends PatientAppointmentDatatable
{
    protected function getAppointments(QueryBuilder $builder, int $patientId): QueryBuilder
    {
        return $this->patientAppointmentDatatableRepository
            ->getHistoryAppointmentsForPatientForDataTable($builder, $patientId);
    }
}
