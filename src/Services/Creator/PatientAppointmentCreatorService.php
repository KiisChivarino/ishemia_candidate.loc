<?php

namespace App\Services\Creator;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\PatientAppointment;
use App\Entity\PlanAppointment;
use App\Entity\Staff;
use App\Repository\PlanAppointmentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PatientAppointmentCreatorService
 * @package App\Services\Creator
 */
class PatientAppointmentCreatorService
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var MedicalRecordCreatorService $medicalRecordCreator */
    private $medicalRecordCreator;

    /** @var PrescriptionCreatorService $prescriptionCreator */
    private $prescriptionCreator;

    /** @var PrescriptionAppointmentCreatorService $prescriptionAppointmentCreator */
    private $prescriptionAppointmentCreator;

    /**
     * PatientAppointmentCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MedicalRecordCreatorService $medicalRecordCreator
     * @param PrescriptionCreatorService $prescriptionCreator
     * @param PrescriptionAppointmentCreatorService $prescriptionAppointmentCreator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicalRecordCreatorService $medicalRecordCreator,
        PrescriptionCreatorService $prescriptionCreator,
        PrescriptionAppointmentCreatorService $prescriptionAppointmentCreator
    ){
        $this->entityManager = $entityManager;
        $this->medicalRecordCreator = $medicalRecordCreator;
        $this->prescriptionCreator = $prescriptionCreator;
        $this->prescriptionAppointmentCreator = $prescriptionAppointmentCreator;
    }

    /**
     * Persist patient appointment
     * @param PatientAppointment $patientAppointment
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     */
    public function persistFirstPatientAppointment(
        PatientAppointment $patientAppointment,
        MedicalHistory $medicalHistory,
        Staff $staff
    ): void
    {
        $this->entityManager->persist(
            $this->prepareFirstPatientAppointment(
                $patientAppointment,
                $this->medicalRecordCreator->persistMedicalRecord($medicalHistory),
                $staff
            )
        );
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return PatientAppointment
     */
    public function createPatientAppointment(MedicalHistory $medicalHistory){
        return (new PatientAppointment())
            ->setMedicalHistory($medicalHistory)
            ->setEnabled(true)
            ->setIsConfirmed(false);
    }

    /**
     * @param PatientAppointment $patientAppointment
     * @param Staff $staff
     * @return PatientAppointment
     */
    public function preparePatientAppointment(PatientAppointment $patientAppointment, Staff $staff): PatientAppointment
    {
        return $patientAppointment
            ->setStaff($staff)
            ->setIsFirst(false);
    }

    /**
     * @param PatientAppointment $patientAppointment
     * @param MedicalRecord $medicalRecord
     * @param Staff $staff
     * @return PatientAppointment
     */
    public function prepareFirstPatientAppointment(
        PatientAppointment $patientAppointment,
        MedicalRecord $medicalRecord,
        Staff $staff
    ): PatientAppointment
    {
        return $patientAppointment
            ->setMedicalRecord($medicalRecord)
            ->setStaff($staff)
            ->setIsFirst(true)
            ->setAppointmentTime((new DateTime())->setTime(0,0));
    }

    /**
     * Persist patient appointments by plan
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     * @return array
     */
    public function persistPatientAppointmentsByPlan(MedicalHistory $medicalHistory, Staff $staff): array
    {
        $patientAppointments = [];
        /** @var PlanAppointmentRepository $planAppointmentRepository */
        $planAppointmentRepository = $this->entityManager->getRepository(PlanAppointment::class);
        /** @var PlanAppointment $appointment */
        foreach ($planAppointmentRepository->getStandardPlanAppointment() as $appointmentPlan) {
            $patientAppointment = $this->createPatientAppointment($medicalHistory);
            $this->preparePatientAppointment($patientAppointment, $staff);
            $this->entityManager->persist($patientAppointment);
            $prescription = $this->prescriptionCreator->createPrescription($medicalHistory, $staff);
            $this->entityManager->persist($prescription);
            $this->entityManager->persist(
                $this->prescriptionAppointmentCreator->createPrescriptionAppointment(
                    $staff,
                    $prescription,
                    $patientAppointment,
                    $appointmentPlan
                )
            );
            $patientAppointments[] = $patientAppointment;
        }
        return $patientAppointments;
    }
}