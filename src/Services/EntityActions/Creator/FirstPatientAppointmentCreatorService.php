<?php

namespace App\Services\EntityActions\Creator;

/**
 * Class FirstPatientAppointmentCreatorService
 * creates first patient appointment
 * @package App\Services\EntityActions\Creator
 */
class FirstPatientAppointmentCreatorService extends PatientAppointmentCreatorService
{
    protected function prepare(): void
    {
       $this->getEntity()
            ->setIsFirst(true)
            ->setIsByPlan(false);
    }
}