<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientAppointment;
use App\Entity\Staff;

/**
 * Class ByDoctorFirstPatientAppointmentCreatorService
 * @package App\Services\EntityActions\Creator
 */
class ByDoctorFirstPatientAppointmentCreatorService extends FirstPatientAppointmentCreatorService
{
    /** @var string Option of doctor creating patient from doctor office */
    public const STAFF_OPTION = 'staff';

    protected function prepare(): void
    {
        parent::prepare();
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $this->getEntity();
        $patientAppointment->setStaff($this->options[self::STAFF_OPTION]);
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(Staff::class, self::STAFF_OPTION);
    }
}