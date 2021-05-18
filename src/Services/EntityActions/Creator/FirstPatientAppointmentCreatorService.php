<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientAppointment;
use App\Entity\PlanAppointment;
use DateTime;

/**
 * Class FirstPatientAppointmentCreatorService
 * creates first patient appointment
 * @package App\Services\EntityActions\Creator
 */
class FirstPatientAppointmentCreatorService extends PatientAppointmentCreatorService
{
    /** @var string Name of first appointment plan option */
    public const FIRST_APPOINTMENT_PLAN_OPTION = 'firstAppointmentPlan';

    protected function prepare(): void
    {
        parent::prepare();
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $this->getEntity();
        $patientAppointment
            ->setIsFirst(true)
            ->setIsByPlan(false)
            ->setAppointmentTime(new DateTime())
            ->setPlanAppointment($this->options[self::FIRST_APPOINTMENT_PLAN_OPTION]);
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(PlanAppointment::class, self::FIRST_APPOINTMENT_PLAN_OPTION);
    }
}