<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;
use Exception;

/**
 * Class DoctorOfficePrescriptionAppointmentService
 * @package App\Services\EntityActions\Creator
 */
class DoctorOfficePrescriptionAppointmentService extends PrescriptionAppointmentCreatorService
{
    /**
     * Actions with entity before persisting one
     */
    protected function prepare(): void
    {
        parent::prepare();
        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $this->getEntity();
        $prescriptionAppointment->setStaff($this->options[self::STAFF_OPTION]);
    }

    /**
     * Options of Doctor Office Creator Service
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(Staff::class, self::STAFF_OPTION);
    }

}