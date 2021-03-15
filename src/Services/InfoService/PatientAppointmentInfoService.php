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
        if ($patientAppointment->getStaff()){
            $staffInfo = 'Врач: ' . AuthUserInfoService::getFIO($patientAppointment->getStaff()->getAuthUser(), true);
        } else{
            $staffInfo = '';
        }
        if ($patientAppointment->getPrescriptionAppointment()) {
            $plannedDateTimeString = $patientAppointment->getPrescriptionAppointment()->getPlannedDateTime()
                ->format(self::APPOINTMENT_TIME_FORMAT);
        } else {
            $plannedDateTimeString = '';
        }
        if ($patientAppointment->getAppointmentTime()) {
            $appointmentTimeString = $patientAppointment->getAppointmentTime()
                ->format(self::APPOINTMENT_TIME_FORMAT);
        } else {
            $appointmentTimeString = '';
        }
        return
            is_null($patientAppointment->getAppointmentTime())
                ?
                $patientInfo . ', ' . $staffInfo . ', ' . $plannedDateTimeString
                :
                $patientInfo . ', ' . $staffInfo . ', ' . $appointmentTimeString;
    }
}