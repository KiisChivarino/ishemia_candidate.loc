<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\PatientMedicine;

/**
 * Class PatientMedicineEditorService
 * @package App\Services\EntityActions\Editor
 */
class PatientMedicineEditorService extends AbstractEditorService
{
    /**
     * PatientMedicineCreatorService constructor.
     */
    public function __construct()
    {
        parent::__construct(PatientMedicine::class);
    }
}