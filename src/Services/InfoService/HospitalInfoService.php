<?php

namespace App\Services\InfoService;

use App\Entity\Hospital;

/**
 * Class HospitalInfoService
 * методы для работы с данными Hospital
 *
 * @package App\Services\InfoService
 */
class HospitalInfoService
{
    /**
     * Checks if hospital is allowed to be deleted
     * @param Hospital $hospital
     * @return bool
     */
    public static function isHospitalDeletable(Hospital $hospital): bool
    {
        return (count($hospital->getPatients()) + count($hospital->getStaff())) === 0;
    }
}
