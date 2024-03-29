<?php

namespace App\Entity;

use App\Repository\PrescriptionMedicineRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrescriptionMedicineRepository::class)
 * @ORM\Table(options={"comment":"Назначение лекарства"});
 * @ORM\HasLifecycleCallbacks();
 */
class PrescriptionMedicine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ назначения препарата"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Prescription::class, inversedBy="prescriptionMedicines")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $prescription;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="prescriptionMedicines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время включения лекарства в назначение"})
     */
    private $inclusionTime;

    /**
     * @ORM\OneToOne(targetEntity=NotificationConfirm::class, inversedBy="prescriptionMedicine")
     * @ORM\JoinColumn(nullable=true)
     */
    private $notificationConfirm;

    /**
     * @ORM\Column(type="date", options={"comment"="Дата начала приема лекарства"})
     */
    private $startingMedicationDate;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата окончания приема лекарства"})
     */
    private $endMedicationDate;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToOne(targetEntity=PatientMedicine::class, inversedBy="prescriptionMedicine", cascade={"persist", "remove"})
     */
    private $patientMedicine;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return DateTimeInterface|null
     */
    public function getInclusionTime(): ?DateTimeInterface
    {
        return $this->inclusionTime;
    }

    /**
     * @param DateTimeInterface $inclusionTime
     * @return $this
     */
    public function setInclusionTime(DateTimeInterface $inclusionTime): self
    {
        $this->inclusionTime = $inclusionTime;

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

    /**
     * @return DateTimeInterface|null
     */
    public function getEndMedicationDate(): ?DateTimeInterface
    {
        return $this->endMedicationDate;
    }

    /**
     * @param DateTimeInterface|null $endMedicationDate
     * @return $this
     */
    public function setEndMedicationDate(?DateTimeInterface $endMedicationDate): self
    {
        $this->endMedicationDate = $endMedicationDate;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getStartingMedicationDate(): ?DateTimeInterface
    {
        return $this->startingMedicationDate;
    }

    /**
     * @param DateTimeInterface $startingMedicationDate
     * @return $this
     */
    public function setStartingMedicationDate(DateTimeInterface $startingMedicationDate): self
    {
        $this->startingMedicationDate = $startingMedicationDate;

        return $this;
    }

    /**
     * @return PatientMedicine|null
     */
    public function getPatientMedicine(): ?PatientMedicine
    {
        return $this->patientMedicine;
    }

    /**
     * @param PatientMedicine $patientMedicine
     * @return $this
     */
    public function setPatientMedicine(PatientMedicine $patientMedicine): self
    {
        $this->patientMedicine = $patientMedicine;
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
