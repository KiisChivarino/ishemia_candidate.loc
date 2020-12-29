<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class MedicalHistoryCreatorService
 * @package App\Services\EntityActions\Creator
 */
class MedicalHistoryCreatorService
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var PatientTestingCreatorService $patientTestingCreator */
    private $patientTestingCreator;

    /** @var PatientAppointmentCreatorService $patientAppointmentCreator */
    private $patientAppointmentCreator;

    /**
     * MedicalHistoryCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param PatientTestingCreatorService $patientTestingCreator
     * @param PatientAppointmentCreatorService $patientAppointmentCreator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PatientTestingCreatorService $patientTestingCreator,
        PatientAppointmentCreatorService $patientAppointmentCreator
    )
    {
        $this->entityManager = $entityManager;
        $this->patientTestingCreator = $patientTestingCreator;
        $this->patientAppointmentCreator = $patientAppointmentCreator;
    }

    /**
     * @return MedicalHistory
     */
    public function createMedicalHistory(): MedicalHistory
    {
        return new MedicalHistory();
    }

    /**
     * Persist new medical history
     * @param MedicalHistory $medicalHistory
     * @param Patient $patient
     * @param Staff $staff
     * @param PatientAppointment $firstPatientAppointment
     * @throws Exception
     */
    public function persistNewMedicalHistory(
        MedicalHistory $medicalHistory,
        Patient $patient,
        Staff $staff,
        PatientAppointment $firstPatientAppointment
    ): void
    {
        $this->entityManager->persist($this->prepareMedicalHistory($medicalHistory, $patient));
        $this->patientTestingCreator->persistFirstPatientTestsByPlan($medicalHistory);
        $this->patientTestingCreator->persistPatientTestsByPlan($medicalHistory, $staff);
        $this->patientAppointmentCreator->persistFirstPatientAppointment(
            $firstPatientAppointment,
            $medicalHistory,
            $staff
        );
        $this->patientAppointmentCreator->persistPatientAppointmentsByPlan($medicalHistory, $staff);
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @param Patient $patient
     * @return MedicalHistory
     */
    private function prepareMedicalHistory(MedicalHistory $medicalHistory, Patient $patient): MedicalHistory
    {
        return $medicalHistory
            ->setPatient($patient)
            ->setEnabled(true)
            ->setDateBegin(new DateTime());
    }
}