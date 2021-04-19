<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\MedicalRecord;
use App\Entity\Prescription;
use App\Services\EntityActions\Core\AbstractEditorService;
use DateTime;
use Exception;

/**
 * Class PrescriptionEditorService
 * edits prescription
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionEditorService extends AbstractEditorService
{
    /** @var string Name of option: entity MedicalRecord */
    public const MEDICAL_RECORD_OPTION_NAME = 'medicalRecord';
    /**
     * Actions with editing prescription before persist
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        if ($prescription->getIsCompleted() && !$prescription->getCompletedTime()) {
            $prescription->setMedicalRecord($this->options[self::MEDICAL_RECORD_OPTION_NAME]);
            $prescription->setCompletedTime(new DateTime());
        }
    }

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
        $this->addOptionCheck(MedicalRecord::class, self::MEDICAL_RECORD_OPTION_NAME);
    }
}