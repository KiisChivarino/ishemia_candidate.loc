<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Staff;
use App\Entity\Patient;

/**
 * Class ByDoctorHospitalPatientCreatorService
 * @package App\Services\EntityActions\Creator
 */
class ByDoctorHospitalPatientCreatorService extends PatientCreatorService
{
    /** @var string Staff option */
    public const STAFF_OPTION = 'staff';

    protected function prepare(): void
    {
        parent::prepare();
        /** @var Patient $patient */
        $patient = $this->getEntity();
        /** @var Staff $staff */
        $staff = $this->options[self::STAFF_OPTION];
        $patient
            ->setHospital($staff->getHospital())
            ->setCity($staff->getHospital()->getCity());
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(Staff::class, self::STAFF_OPTION);
    }
}