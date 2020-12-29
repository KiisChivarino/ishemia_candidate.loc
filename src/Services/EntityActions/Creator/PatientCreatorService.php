<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientCreatorService
{
    /** @var MedicalHistoryCreatorService $medicalHistoryCreator */
    private $medicalHistoryCreator;

    /** @var PatientAppointmentCreatorService $patientAppointmentCreator */
    private $patientAppointmentCreator;

    /** @var PatientTestingCreatorService $patientTestingCreator */
    private $patientTestingCreator;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * PatientCreatorService constructor.
     * @param MedicalHistoryCreatorService $medicalHistoryCreator
     * @param PatientAppointmentCreatorService $patientAppointmentCreator
     * @param PatientTestingCreatorService $patientTestingCreator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MedicalHistoryCreatorService $medicalHistoryCreator,
        PatientAppointmentCreatorService $patientAppointmentCreator,
        PatientTestingCreatorService $patientTestingCreator,
        EntityManagerInterface $entityManager
    )
    {
        $this->medicalHistoryCreator = $medicalHistoryCreator;
        $this->patientAppointmentCreator = $patientAppointmentCreator;
        $this->patientTestingCreator = $patientTestingCreator;
        $this->entityManager = $entityManager;
    }

    /**
     * Create Patient entity object
     * @return Patient
     */
    public function createPatient(): Patient
    {
        return new Patient();
    }

    /**
     * Persist new patient
     * @param Patient $patient
     * @param AuthUser $patientAuthUser
     * @param MedicalHistory $medicalHistory
     * @param PatientAppointment $firstPatientAppointment
     * @param Staff $staff
     * @throws Exception
     */
    public function persistNewPatient(
        Patient $patient,
        AuthUser $patientAuthUser,
        MedicalHistory $medicalHistory,
        PatientAppointment $firstPatientAppointment,
        Staff $staff
    ): void
    {
        $this->preparePatient($patient, $patientAuthUser);
        $this->medicalHistoryCreator->persistNewMedicalHistory($medicalHistory, $patient, $staff, $firstPatientAppointment);
        $this->entityManager->persist($patient);
    }

    /**
     * Prepare patient entity object
     * @param Patient $patient
     * @param AuthUser $patientAuthUser
     * @return Patient
     */
    public function preparePatient(Patient $patient, AuthUser $patientAuthUser): Patient
    {
        return $patient
            ->setAuthUser($patientAuthUser)
            ->setSmsInforming(true)
            ->setEmailInforming(true);
    }
}