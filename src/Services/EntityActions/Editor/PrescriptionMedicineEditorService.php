<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\PrescriptionMedicine;
use Exception;

/**
 * Class PrescriptionMedicineEditorService
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionMedicineEditorService extends AbstractEditorService
{
    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PrescriptionMedicine::class);
    }
}