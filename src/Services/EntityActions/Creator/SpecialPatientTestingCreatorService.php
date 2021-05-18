<?php

namespace App\Services\EntityActions\Creator;

/**
 * Class SpecialPrescriptionTestingCreatorService
 * @package App\Services\EntityActions\Creator
 */
class SpecialPatientTestingCreatorService extends PatientTestingCreatorService
{
    protected function prepare(): void
    {
        parent::prepare();
        $this->getEntity()
            ->setIsFirst(false)
            ->setIsByPlan(false);
    }
}