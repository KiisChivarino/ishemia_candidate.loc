<?php

namespace App\Entity;

use App\Repository\PatientNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Patient Notification
 * @ORM\Entity(repositoryClass=PatientNotificationRepository::class)
 * @ORM\Table(options={"comment":"Уведомление пациента"});
 */
class PatientNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ уведомления пациента"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="patientNotification", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $notification;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientNotifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="patientNotifications")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $medicalRecord;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="patientNotifications")
     */
    private $medicalHistory;

    /**
     * @ORM\ManyToOne(targetEntity=NotificationConfirm::class, inversedBy="patientNotification")
     */
    private $notificationConfirm;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Notification|null
     */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function setNotification(Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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
     * @return $this
     */
    public function setMedicalRecord(?MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;

        return $this;
    }

    /**
     * @return MedicalHistory|null
     */
    public function getMedicalHistory(): ?MedicalHistory
    {
        return $this->medicalHistory;
    }

    /**
     * @param MedicalHistory|null $medicalHistory
     * @return $this
     */
    public function setMedicalHistory(?MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;

        return $this;
    }

    public function getNotificationConfirm(): ?NotificationConfirm
    {
        return $this->notificationConfirm;
    }

    public function setNotificationConfirm(?NotificationConfirm $notificationConfirm): self
    {
        $this->notificationConfirm = $notificationConfirm;

        return $this;
    }
}
