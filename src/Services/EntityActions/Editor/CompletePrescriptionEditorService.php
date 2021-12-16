<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Prescription;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use DateTime;
use Exception;

/**
 * Class CompletePrescriptionEditorService
 *
 * RU: Обработка факта завершения общего назначения
 * ENG: Processing the fact of completion of prescription
 *
 * @package App\Services\EntityActions\Editor
 */
class CompletePrescriptionEditorService extends PrescriptionEditorService
{
    /** @var string Name of option: entity MedicalRecord */
    public const MEDICAL_RECORD_CREATOR_OPTION_NAME = 'medicalRecordCreator';

    /**
     * Actions with editing prescription before persist
     * @throws Exception
     */
    protected function prepare(): void
    {
        parent::prepare();

        /** @var Prescription $prescription */
        $prescription = $this->getEntity();

        /** @var MedicalRecordCreatorService $medicalRecordCreator */
        $medicalRecordCreator = $this->options[self::MEDICAL_RECORD_CREATOR_OPTION_NAME];

        $prescription->setMedicalRecord(
            $medicalRecordCreator->execute(
                [
                    MedicalRecordCreatorService::MEDICAL_HISTORY_OPTION_NAME => $prescription->getMedicalHistory(),
                ]
            )->getEntity()
        );

        $prescription->setCompletedTime(new DateTime());
    }

    /**
     * Registers options
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(MedicalRecordCreatorService::class, self::MEDICAL_RECORD_CREATOR_OPTION_NAME);
    }
}