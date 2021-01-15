<?php

namespace App\Services\Notification;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Patient;

class NotificationData
{
    /** @var Patient */
    private $patientReceiver;

    /** @var MedicalHistory */
    private $medicalHistory;

    /** @var MedicalRecord */
    private $medicalRecord;

    public function __construct(Patient $patient, MedicalHistory $medicalHistory, MedicalRecord $medicalRecord = null)
    {
        $this->patientReceiver = $patient;
        $this->medicalHistory = $medicalHistory;
        $this->medicalRecord = $medicalRecord;
    }

    /**
     * @param Patient $patient
     * @return NotificationData
     */
    public function setPatientReceiver(Patient $patient): self
    {
        $this->patientReceiver = $patient;
        return $this;
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return NotificationData
     */
    public function setMedicalHistory(MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }

    /**
     * @param MedicalRecord|null $medicalRecord
     * @return NotificationData
     */
    public function setMedicalRecord(?MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * @return Patient
     */
    public function getPatientReceiver(): Patient
    {
        return $this->patientReceiver;
    }

    /**
     * @return MedicalHistory
     */
    public function getMedicalHistory(): MedicalHistory
    {
        return $this->medicalHistory;
    }

    /**
     * @return MedicalRecord|null
     */
    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
    }
}