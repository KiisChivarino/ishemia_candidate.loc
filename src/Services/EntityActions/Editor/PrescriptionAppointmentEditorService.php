<?php


namespace App\Services\EntityActions\Editor;


use Exception;

class PrescriptionAppointmentEditorService extends AbstractEditorService
{
    /**
     * @const string
     */
    public const STAFF_OPTION = 'staff';

    /**
     * @const string
     */
    public const PRESCRIPTION_OPTION = 'prescription';

    /**
     * @const string
     */
    public const PATIENT_APPOINTMENT_OPTION = 'patientAppointment';

    /**
     * Actions with editing prescription appointment before persist
     * @throws Exception
     */
    protected function prepare(): void
    {
//        /** @var PrescriptionAppointment $prescriptionAppointment */
//        $prescriptionAppointment = $this->getEntity();
//
//        if ($prescription->getIsCompleted() && !$prescription->getCompletedTime()) {
//            $prescription->setMedicalRecord($this->options[self::MEDICAL_RECORD_OPTION_NAME]);
//            $prescription->setCompletedTime(new DateTime());
//        }
    }

    /**
     * Registers options
     */
    protected function configureOptions(): void
    {
//        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
//        $this->addOptionCheck(PatientAppointment::class, self::PATIENT_APPOINTMENT_OPTION);
    }
}