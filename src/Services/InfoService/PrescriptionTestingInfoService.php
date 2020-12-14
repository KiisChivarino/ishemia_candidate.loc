<?php

namespace App\Services\InfoService;

use App\Entity\PrescriptionTesting;

/**
 * Class PrescriptionTestingInfoService
 * @package App\Services\InfoService
 */
class PrescriptionTestingInfoService
{
    /**
     * @param PrescriptionTesting $prescriptionTesting
     * @return string
     */
    public static function getPrescriptionTestingTitle(PrescriptionTesting $prescriptionTesting): string
    {
        return
            'Пациент: ' .AuthUserInfoService::getFIO(
                $prescriptionTesting->getPrescription()->getMedicalHistory()->getPatient()->getAuthUser()
            ).', '
            . 'Планируемая дата обследования: ' .$prescriptionTesting->getPlannedDate()->format('d.m.Y').', '
            . 'Обследование: '.$prescriptionTesting->getPatientTesting()->getAnalysisGroup()->getName();
    }
}