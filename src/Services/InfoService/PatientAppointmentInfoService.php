<?php

namespace App\Services\InfoService;

use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Repository\PrescriptionAppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PatientAppointmentInfoService
 * @package App\Services\InfoService
 */
class PatientAppointmentInfoService
{
    /** @var string Format of patient appointment time */
    public const APPOINTMENT_TIME_FORMAT = 'd.m.Y';

    /**
     * PatientAppointmentInfoService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get patient appointment info string
     * @param PatientAppointment $patientAppointment
     * @return string
     */
    static public function getPatientAppointmentInfoString(PatientAppointment $patientAppointment): string
    {
        $patientInfo = 'Пациент: '
            . AuthUserInfoService::getFIO(
                $patientAppointment->getMedicalHistory()->getPatient()->getAuthUser(),
                true
            );
        $staff = $patientAppointment->getStaff();
        $staffInfo = $staff
            ? 'Врач: ' . AuthUserInfoService::getFIO($staff->getAuthUser(), true)
            : '';
        $prescriptionAppointment = $patientAppointment->getPrescriptionAppointment();
        $plannedDateTimeString = $prescriptionAppointment
            ? $prescriptionAppointment->getPlannedDateTime()
                ->format(self::APPOINTMENT_TIME_FORMAT)
            : '';
        $appointmentTime = $patientAppointment->getAppointmentTime();
        $appointmentTimeString = $appointmentTime
            ? $appointmentTime->format(self::APPOINTMENT_TIME_FORMAT)
            : '';
        return
            is_null($patientAppointment->getAppointmentTime())
                ?
                $patientInfo . ', ' . $staffInfo . ', ' . $plannedDateTimeString
                :
                $patientInfo . ', ' . $staffInfo . ', ' . $appointmentTimeString;
    }

    /**
     * @param Prescription $prescription
     * @param PrescriptionAppointmentRepository $prescriptionAppointmentRepository
     * @return bool
     */
    public static function isAppointmentNotExists(
        Prescription $prescription,
        PrescriptionAppointmentRepository $prescriptionAppointmentRepository
    ): bool
    {
        return $prescriptionAppointmentRepository->countPrescriptionAppointmentsByPrescription($prescription) == 0;
    }
}