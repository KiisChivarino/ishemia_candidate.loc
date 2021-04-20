<?php

namespace App\Services\InfoService;

use App\Entity\Prescription;

/**
 * Class PrescriptionInfoService
 *
 * @package App\Services\InfoService
 */
class PrescriptionInfoService
{
    /**
     * Return prescription title
     *
     * @param Prescription $prescription
     *
     * @return string
     */
    static public function getPrescriptionTitle(Prescription $prescription): string
    {
        return
            'Врач: ' .
            (new AuthUserInfoService())->getFIO($prescription->getStaff()->getAuthUser()) .
            ', История болезни: ' .
            (new MedicalHistoryInfoService())->getMedicalHistoryTitle($prescription->getMedicalHistory());
    }

    /**
     * Count children of prescription
     *
     * @param Prescription $prescription
     *
     * @return int
     */
    static public function countChildren(Prescription $prescription): int
    {
        return
        //TODO Разобраться с ошибкой
//            $prescription->getPrescriptionMedicines()->count() +
            $prescription->getPrescriptionTestings()->count() +
            $prescription->getPrescriptionAppointments()->count();
    }

    /**
     * Check for exists special prescriptions
     * @param Prescription $prescription
     * @return bool
     */
    static public function isSpecialPrescriptionsExists(Prescription $prescription): bool
    {
        return self::countChildren($prescription) > 0;
    }
}