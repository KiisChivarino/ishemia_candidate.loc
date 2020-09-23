<?php

namespace App\Services\InfoService;

use App\Entity\MedicalRecord;

/**
 * Class MedicalRecordInfoService
 *
 * @package App\Services\InfoService
 */
class MedicalRecordInfoService
{
    /**
     * Returns medical record title
     *
     * @param MedicalRecord $medicalRecord
     *
     * @return string
     */
    public function getMedicalRecordTitle(MedicalRecord $medicalRecord): string
    {
        return
            (new MedicalHistoryInfoService())->getMedicalHistoryTitle($medicalRecord->getMedicalHistory())
            .', '.
            $medicalRecord->getRecordDate()->format('d.m.Y');
    }
}