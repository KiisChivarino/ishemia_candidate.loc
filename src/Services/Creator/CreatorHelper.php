<?php

namespace App\Services\Creator;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Class CreatorHelper
 * @package App\Services\Creator
 */
class CreatorHelper
{
    /** @var string Property dateBegin of medical history */
    protected const MEDICAL_HISTORY_DATE_BEGIN_PROPERTY = 'dateBegin';

    /** @var string Property heartAttackDate of patient */
    protected const PATIENT_HEART_ATTACK_DATE_PROPERTY = 'heartAttackDate';

    /**
     * Get planned date
     * @param DateTimeInterface $startingPointDate
     * @param int $timeRangeCount
     * @param int $multiplier
     * @param string $format
     *
     * @return DateTime|null
     */
    static public function getPlannedDate(
        DateTimeInterface $startingPointDate,
        int $timeRangeCount,
        int $multiplier,
        string $format
    ): ?DateTime
    {
        try {
            return $startingPointDate->add(
                new DateInterval(
                    'P' .
                    (string)($timeRangeCount * $multiplier) .
                    $format
                )
            )->setTime(0, 0, 0);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get starting point date
     * @param string $startingPointName
     * @param DateTimeInterface $medicalHistoryDateBegin
     * @param DateTimeInterface $patientHeartAttackDate
     * @return DateTimeInterface
     * @throws Exception
     */
    static public function getStartingPointDate(
        string $startingPointName,
        DateTimeInterface $medicalHistoryDateBegin,
        DateTimeInterface $patientHeartAttackDate
    )
    {
        switch ($startingPointName) {
            case self::MEDICAL_HISTORY_DATE_BEGIN_PROPERTY :
                return $medicalHistoryDateBegin;
            case self::PATIENT_HEART_ATTACK_DATE_PROPERTY:
                return $patientHeartAttackDate;
            default:
                throw new Exception('Не удалось получить точку отсчета для обследования по плану!');
        }
    }
}