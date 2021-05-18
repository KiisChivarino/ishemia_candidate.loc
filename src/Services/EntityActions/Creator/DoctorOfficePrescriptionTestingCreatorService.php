<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Staff;
use Exception;

/**
 * Class DoctorOfficePrescriptionTestingService
 * @package App\Services\EntityActions\Creator
 */
class DoctorOfficePrescriptionTestingCreatorService extends PrescriptionTestingCreatorService
{
    /**
     * @const string
     */
    public const STAFF_OPTION = 'staff';

    /**
     * Actions with entity before persisting one
     */
    protected function prepare(): void
    {
        parent::prepare();
        $this->getEntity()->setStaff($this->options[self::STAFF_OPTION]);
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