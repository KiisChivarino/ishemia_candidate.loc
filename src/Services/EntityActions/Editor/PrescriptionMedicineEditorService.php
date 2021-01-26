<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\PrescriptionMedicine;

/**
 * Class PrescriptionMedicineEditorService
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionMedicineEditorService extends AbstractEditorService
{
    /**
     * PrescriptionCreatorService constructor.
     */
    public function __construct()
    {
        parent::__construct(PrescriptionMedicine::class);
    }
}