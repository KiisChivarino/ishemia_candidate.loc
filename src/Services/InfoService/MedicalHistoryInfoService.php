<?php

namespace App\Services\InfoService;

use App\Entity\MedicalHistory;

/**
 * Class MedicalHistoryInfoService
 *
 * @package App\Services\InfoService
 */
class MedicalHistoryInfoService
{
    /**
     * Returns label of MedicalHistory
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return string
     */
    static public function getMedicalHistoryTitle(MedicalHistory $medicalHistory): string
    {
        return (new AuthUserInfoService())
                ->getFIO($medicalHistory->getPatient()->getAuthUser(), true)
            . ': '
            . $medicalHistory->getDateBegin()->format('d.m.Y');
    }
}