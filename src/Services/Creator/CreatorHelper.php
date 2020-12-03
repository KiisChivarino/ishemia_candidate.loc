<?php

namespace App\Services\Creator;

use DateInterval;
use DateTime;
use Exception;

/**
 * Class CreatorHelper
 * @package App\Services\Creator
 */
class CreatorHelper
{
    /**
     * Get planned date
     * @param DateTime $currDate
     * @param int $timeRangeCount
     * @param int $multiplier
     * @param string $format
     *
     * @return DateTime|null
     * @throws Exception
     */
    static public function getPlannedDate(
        DateTime $currDate,
        int $timeRangeCount,
        int $multiplier,
        string $format
    ): ?DateTime
    {
        try {
            return $currDate->add(
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