<?php

namespace App\Services\Notification;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Entity\Patient;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ObjectManager;

/**
 * Class NotificationData
 * @package App\Services\Notification
 */
class NotificationData
{
    /** @var Patient */
    private $patientReceiver;

    /** @var MedicalHistory */
    private $medicalHistory;

    /** @var MedicalRecord */
    private $medicalRecord;

    /** @var int */
    private $groupId;

    /**
     * NotificationData constructor.
     * @param ObjectManager $em
     * @param Patient $patient
     * @param MedicalHistory $medicalHistory
     * @param MedicalRecord|null $medicalRecord
     * @throws NonUniqueResultException
     */
    public function __construct(
        ObjectManager $em,
        Patient $patient,
        MedicalHistory $medicalHistory,
        MedicalRecord $medicalRecord = null
    )
    {
        $lastNotification = $em->getRepository(Notification::class)->findLastGroup();
        $this->groupId = $lastNotification ? $lastNotification->getGroupId() + 1 : 1;
        $this->patientReceiver = $patient;
        $this->medicalHistory = $medicalHistory;
        $this->medicalRecord = $medicalRecord;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return Patient
     */
    public function getPatientReceiver(): Patient
    {
        return $this->patientReceiver;
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
     * @return MedicalHistory
     */
    public function getMedicalHistory(): MedicalHistory
    {
        return $this->medicalHistory;
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
     * @return MedicalRecord|null
     */
    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
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
}