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
}