<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Prescription;
use App\Services\EntityActions\Core\AbstractEditorService;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
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
    public const MEDICAL_RECORD_CREATOR_OPTION_NAME = 'medicalRecordCreator';

    /**
     * Actions with editing prescription before persist
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        $this->completePrescription();
        /** @var MedicalRecordCreatorService $medicalRecordCreator */
        $medicalRecordCreator = $this->options[self::MEDICAL_RECORD_CREATOR_OPTION_NAME];
        if ($prescription->getIsCompleted() && !$prescription->getCompletedTime()) {
            $prescription->setMedicalRecord(
                $medicalRecordCreator->execute(
                    [
                        MedicalRecordCreatorService::MEDICAL_HISTORY_OPTION_NAME => $prescription->getMedicalHistory(),
                    ]
                )->getEntity()
            );
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
        $this->addOptionCheck(MedicalRecordCreatorService::class, self::MEDICAL_RECORD_CREATOR_OPTION_NAME);
    }
}