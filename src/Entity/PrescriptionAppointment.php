<?php

namespace App\Entity;

use App\Repository\PrescriptionAppointmentRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrescriptionAppointmentRepository::class)
 * @ORM\Table(options={"comment":"Назначение на прием"});
 */
class PrescriptionAppointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ назначения на прием"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Prescription::class, inversedBy="prescriptionAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prescription;

    /**
     * @ORM\OneToOne(targetEntity=PatientAppointment::class, inversedBy="prescriptionAppointment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientAppointment;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="prescriptionAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время включения в назначение"})
     */
    private $inclusionTime;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг подтверждения врачом назначения на прием", "default"=false})
     */
    private $confirmedByStaff;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Назначенные дата и время проведения приема"})
     */
    private $plannedDateTime;

    /**
     * @ORM\OneToOne(targetEntity=NotificationConfirm::class, inversedBy="prescriptionAppointment")
     * @ORM\JoinColumn(nullable=true)
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
     * @return DateTimeInterface|null
     */
    public function getInclusionTime(): ?\DateTimeInterface
    {
        return $this->inclusionTime;
    }

    /**
     * @param DateTimeInterface $inclusionTime
     * @return $this
     */
    public function setInclusionTime(\DateTimeInterface $inclusionTime): self
    {
        $this->inclusionTime = $inclusionTime;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConfirmedByStaff(): ?bool
    {
        return $this->confirmedByStaff;
    }

    /**
     * @param bool $confirmedByStaff
     * @return $this
     */
    public function setConfirmedByStaff(bool $confirmedByStaff): self
    {
        $this->confirmedByStaff = $confirmedByStaff;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPlannedDateTime(): ?\DateTimeInterface
    {
        return $this->plannedDateTime;
    }

    /**
     * @param DateTimeInterface $plannedDateTime
     * @return $this
     */
    public function setPlannedDateTime(\DateTimeInterface $plannedDateTime): self
    {
        $this->plannedDateTime = $plannedDateTime;

        return $this;
    }

    /**
     * @return Prescription|null
     */
    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    /**
     * @param Prescription|null $prescription
     * @return $this
     */
    public function setPrescription(?Prescription $prescription): self
    {
        $this->prescription = $prescription;

        return $this;
    }

    public function getPatientAppointment(): ?PatientAppointment
    {
        return $this->patientAppointment;
    }

    /**
     * @param PatientAppointment $patientAppointment
     * @return $this
     */
    public function setPatientAppointment(PatientAppointment $patientAppointment): self
    {
        $this->patientAppointment = $patientAppointment;

        return $this;
    }

    /**
     * @return Staff|null
     */
    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    /**
     * @param Staff|null $staff
     * @return $this
     */
    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * @return NotificationConfirm|null
     */
    public function getNotificationConfirm(): ?NotificationConfirm
    {
        return $this->notificationConfirm;
    }

    /**
     * @param NotificationConfirm|null $notificationConfirm
     * @return $this
     */
    public function setNotificationConfirm(?NotificationConfirm $notificationConfirm): self
    {
        $this->notificationConfirm = $notificationConfirm;

        return $this;
    }
}
