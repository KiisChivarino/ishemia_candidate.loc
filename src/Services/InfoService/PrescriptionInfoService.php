<?php

namespace App\Services\InfoService;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Entity\PrescriptionTesting;

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
            'Врач: '.
            (new AuthUserInfoService())->getFIO($prescription->getStaff()->getAuthUser()).
            ', История болезни: '.
            (new MedicalHistoryInfoService())->getMedicalHistoryTitle($prescription->getMedicalHistory());
    }

    /**
     * Count children of prescription
     *
     * @param Prescription $prescription
     *
     * @return int
     */
    static public function countChildren(Prescription $prescription)
    {
        $prescriptionMedicines = count(
            array_filter(
                $prescription->getPrescriptionMedicines()->toArray(),
                function (PrescriptionMedicine $prescriptionMedicine) {
                    return $prescriptionMedicine->getEnabled();
                }
            )
        );
        $prescriptionTestings = count(
            array_filter(
                $prescription->getPrescriptionTestings()->toArray(),
                function (PrescriptionTesting $prescriptionTesting) {
                    return $prescriptionTesting->getEnabled();
                }
            )
        );
        return $prescriptionMedicines + $prescriptionTestings;
    }
}