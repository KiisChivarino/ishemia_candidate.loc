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
     * @param MedicalRecord|null $medicalRecord
     *
     * @return string
     */
    static public function getMedicalRecordTitle(?MedicalRecord $medicalRecord): string
    {
        return
            $medicalRecord ?
                (new MedicalHistoryInfoService())->getMedicalHistoryTitle($medicalRecord->getMedicalHistory())
                .', '.
                $medicalRecord->getRecordDate()->format('d.m.Y') : '';
    }
}