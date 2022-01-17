<?php

namespace App\Services\InfoService;

use App\Entity\PatientMedicine;

class PatientMedicineInfoService
{
    /**
     * Get patient appointment info string
     *
     * @param PatientMedicine $patientMedicine
     *
     * @return string
     */
    static public function getPatientMedicineInfoString(PatientMedicine $patientMedicine): string
    {
        $patientInfo = 'Пациент: '
            . AuthUserInfoService::getFIO(
                $patientMedicine
                    ->getPrescriptionMedicine()
                    ->getPrescription()
                    ->getMedicalHistory()
                    ->getPatient()
                    ->getAuthUser(),
                true
            );
        $staff = $patientMedicine->getPrescriptionMedicine()->getStaff();
        $staffInfo = $staff
            ? 'Врач: ' . AuthUserInfoService::getFIO($staff->getAuthUser(), true)
            : '';
        return $patientInfo . ', ' . $staffInfo . ', ' . $patientMedicine->getMedicineName();
    }
}