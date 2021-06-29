<?php

namespace App\Entity;

use App\Repository\PrescriptionTestingRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrescriptionTestingRepository::class)
 * @ORM\Table(options={"comment":"Назначение обследования"});
 * @ORM\HasLifecycleCallbacks();
 */
class PrescriptionTesting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ назначения обследования"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Prescription::class, inversedBy="prescriptionTestings")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $prescription;

    /**
     * @ORM\OneToOne(targetEntity=PatientTesting::class, inversedBy="prescriptionTesting", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientTesting;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="prescriptionTestings")
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
     * @ORM\Column(type="date", options={"comment"="Назначенная дата проведения обследования"})
     */
    private $plannedDate;

    /**
     * @ORM\OneToOne(targetEntity=NotificationConfirm::class, inversedBy="prescriptionTesting")
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
     * @return Prescription|null
     */
    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    /**
     * @param Prescription|null $prescription
     *
     * @return $this
     */
    public function setPrescription(?Prescription $prescription): self
    {
        $this->prescription = $prescription;
        return $this;
    }

    /**
     * @return PatientTesting|null
     */
    public function getPatientTesting(): ?PatientTesting
    {
        return $this->patientTesting;
    }

    /**
     * @param PatientTesting $patientTesting
     *
     * @return $this
     */
    public function setPatientTesting(PatientTesting $patientTesting): self
    {
        $this->patientTesting = $patientTesting;
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
     *
     * @return $this
     */
    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getInclusionTime(): ?DateTimeInterface
    {
        return $this->inclusionTime;
    }

    /**
     * @param DateTimeInterface $inclusionTime
     *
     * @return $this
     */
    public function setInclusionTime(DateTimeInterface $inclusionTime): self
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
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPlannedDate(): ?DateTimeInterface
    {
        return $this->plannedDate;
    }

    /**
     * @param DateTimeInterface $plannedDate
     * @return $this
     */
    public function setPlannedDate(DateTimeInterface $plannedDate): self
    {
        $this->plannedDate = $plannedDate;
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
     * @ORM\PrePersist
     */
    public function PrePersist(): void
    {
        $this->inclusionTime = new DateTime();
    }
}
