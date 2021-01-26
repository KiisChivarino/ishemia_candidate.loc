<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientMedicine;

/**
 * Class PatientMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientMedicineCreatorService extends AbstractCreatorService
{
    /**
     * PatientMedicineCreatorService constructor.
     */
    public function __construct()
    {
        parent::__construct(PatientMedicine::class);
    }
}