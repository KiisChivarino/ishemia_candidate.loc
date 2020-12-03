<?php

namespace App\Services\Creator;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientCreatorService
 * @package App\Services\Creator
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
     * Persist patient
     * @param Patient $patient
     * @param AuthUser $patientAuthUser
     * @param MedicalHistory $medicalHistory
     * @param PatientAppointment $firstPatientAppointment
     * @param Staff $staff
     * @throws Exception
     */
    public function persistPatient(
        Patient $patient,
        AuthUser $patientAuthUser,
        MedicalHistory $medicalHistory,
        PatientAppointment $firstPatientAppointment,
        Staff $staff
    ): void
    {
        $this->preparePatient($patient, $patientAuthUser);
        $this->medicalHistoryCreator->persistMedicalHistory($medicalHistory, $patient);
        $this->patientTestingCreator->persistFirstPatientTestsByPlan($medicalHistory);
        $this->patientTestingCreator->persistPatientTestsByPlan($medicalHistory, $staff);
        $this->patientAppointmentCreator->persistFirstPatientAppointment($firstPatientAppointment, $medicalHistory, $staff);
        $this->patientAppointmentCreator->persistPatientAppointmentsByPlan($medicalHistory, $staff);
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