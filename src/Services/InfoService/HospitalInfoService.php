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
        if (count($hospital->getPatients()) === 0 && count($hospital->getStaff()) === 0) {
            return true;
        }
        return false;
    }
}
