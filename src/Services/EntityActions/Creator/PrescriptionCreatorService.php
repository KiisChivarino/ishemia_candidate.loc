<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Entity\Staff;
use DateTime;

/**
 * Class PrescriptionCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionCreatorService
{
    /**
     * Create Prescription entity object
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     * @return Prescription
     */
    public function createPrescription(MedicalHistory $medicalHistory, Staff $staff): Prescription
    {
        return (new Prescription())
            ->setMedicalHistory($medicalHistory)
            ->setEnabled(true)
            ->setIsCompleted(false)
            ->setStaff($staff)
            ->setCreatedTime(new DateTime())
            ->setIsPatientConfirmed(false);
    }
}