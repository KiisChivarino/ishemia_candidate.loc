<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PrescriptionMedicine;
use App\Entity\Staff;
use Exception;

/**
 * Class DoctorOfficePrescriptionMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class DoctorOfficePrescriptionMedicineCreatorService extends PrescriptionMedicineCreatorService
{
    /**
     * Actions with entity before persisting one
     * @throws Exception
     */
    protected function prepare(): void
    {
        parent::prepare();
        /** @var PrescriptionMedicine $prescriptionMedicine */
        $prescriptionMedicine = $this->getEntity();
        $prescriptionMedicine->setStaff($this->options[self::STAFF_OPTION]);
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