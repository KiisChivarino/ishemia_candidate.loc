<?php

namespace App\Services\InfoService;

use App\Entity\MedicalHistory;
use DateInterval;
use DateTime;
use Exception;

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
    public function getMedicalHistoryTitle(MedicalHistory $medicalHistory): string
    {
        return (new AuthUserInfoService())->getFIO($medicalHistory->getPatient()->getAuthUser(), true).': '.$medicalHistory->getDateBegin()->format('d.m.Y');
    }

    /**
     * @param DateTime $currDate
     * @param int $timeRangeCount
     * @param int $multiplier
     * @param string $format
     *
     * @return DateTime
     * @throws Exception
     */
    public function getPlannedDate(DateTime $currDate, int $timeRangeCount, int $multiplier, string $format)
    {
        return $currDate->add(
            new DateInterval(
                'P'.
                (string)($timeRangeCount * $multiplier).
                $format
            )
        );
    }
}