<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;

/**
 * Class SpecialPatientAppointmentCreatorService
 * creates a handmade patient appointment
 * @package App\Services\EntityActions\Creator
 */
class SpecialPatientAppointmentCreatorService extends PatientAppointmentCreatorService
{
    /** @var string Name of Staff option */
    public const STAFF_OPTION = 'staff';

    /** @var string Name of Prescription appointment option */
    public const PRESCRIPTION_APPOINTMENT_OPTION = 'prescriptionAppointment';

    /**
     * Actions with patient appointment after submitting form before persist
     */
    protected function prepare(): void
    {
        parent::prepare();
        $this->getEntity()
            ->setIsFirst(false)
            ->setIsByPlan(false)
            ->setStaff($this->options[self::STAFF_OPTION])
            ->setPrescriptionAppointment($this->options[self::PRESCRIPTION_APPOINTMENT_OPTION]);
    }

    /**
     * Options with data for service
     */
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(Staff::class, self::STAFF_OPTION);
        $this->addOptionCheck(PrescriptionAppointment::class, self::PRESCRIPTION_APPOINTMENT_OPTION);
    }
}