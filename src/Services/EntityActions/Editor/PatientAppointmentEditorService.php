<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\PatientAppointment;
use App\Services\EntityActions\Core\AbstractEditorService;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use Exception;

/**
 * Class PatientAppointmentEditorService
 * @package App\Services\EntityActions\Editor
 */
class PatientAppointmentEditorService extends AbstractEditorService
{
    /** @var string Name of option: entity MedicalRecord */
    public const MEDICAL_RECORD_CREATOR_OPTION_NAME = 'medicalRecordCreator';

    /**
     * Actions with editing prescription before persist
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $this->getEntity();
        /** @var MedicalRecordCreatorService $medicalRecordCreator */
        $medicalRecordCreator = $this->options[self::MEDICAL_RECORD_CREATOR_OPTION_NAME];
        if ($patientAppointment->getIsProcessedByStaff() and !$patientAppointment->getIsMissed()) {
            $patientAppointment->setMedicalRecord(
                $medicalRecordCreator->execute(
                    [
                        MedicalRecordCreatorService::MEDICAL_HISTORY_OPTION_NAME => $patientAppointment->getMedicalHistory(),
                    ]
                )->getEntity()
            );
        }
    }

    /**
     * Sets completed status for prescription
     */
    public function missingPatientAppointment(): self
    {
        $this->getEntity()->setIsMissed(true);
        $this->getEntity()->setIsProcessedByStaff(true);
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