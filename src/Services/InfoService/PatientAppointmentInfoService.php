<?php

namespace App\Services\InfoService;

use App\Entity\PatientAppointment;

/**
 * Class PatientAppointmentInfoService
 * @package App\Services\InfoService
 */
class PatientAppointmentInfoService
{
    /** @var string Format of patient appointment time */
    public const APPOINTMENT_TIME_FORMAT = 'd.m.Y';

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
        $getAppointmentTime = $patientAppointment->getAppointmentTime();
        $appointmentTimeString = $getAppointmentTime
            ? $getAppointmentTime->format(self::APPOINTMENT_TIME_FORMAT)
            : '';
        return
            is_null($patientAppointment->getAppointmentTime())
                ?
                $patientInfo . ', ' . $staffInfo . ', ' . $plannedDateTimeString
                :
                $patientInfo . ', ' . $staffInfo . ', ' . $appointmentTimeString;
    }
}