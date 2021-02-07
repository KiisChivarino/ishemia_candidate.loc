<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\PatientMedicine;
use Exception;

/**
 * Class PatientMedicineEditorService
 * @package App\Services\EntityActions\Editor
 */
class PatientMedicineEditorService extends AbstractEditorService
{
    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PatientMedicine::class);
    }
}