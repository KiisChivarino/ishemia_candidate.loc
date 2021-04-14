<?php


namespace App\Services\EntityActions\Creator;


use App\Entity\Staff;
use Exception;

/**
 * Class DoctorOfficePrescriptionService
 * @package App\Services\EntityActions\Creator
 */
class DoctorOfficePrescriptionService extends PrescriptionCreatorService
{
    /**
     * Actions with entity before persisting one
     */
    protected function prepare(): void
    {
        parent::prepare();
        /** Executes without form */
        if (!$this->getEntity()->getStaff()) {
            $this->getEntity()->setStaff($this->options[self::STAFF_OPTION]);
        }
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