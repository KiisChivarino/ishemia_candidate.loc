<?php

namespace App\Services\EntityActions\Editor;

use App\Services\EntityActions\Core\AbstractEditorService;

/**
 * Class PrescriptionEditorService
 * edits prescription
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionEditorService extends AbstractEditorService
{
    /**
     * Sets completed status for prescription
     */
    public function completePrescription(): self
    {
        $this->getEntity()->setIsCompleted(true);
        return $this;
    }

    /**
     * Registers options
     */
    protected function configureOptions(): void
    {
    }
}