<?php

namespace App\Entity;

use App\Repository\PrescriptionMedicineRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrescriptionMedicineRepository::class)
 * @ORM\Table(options={"comment":"Назначение лекарства"});
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $prescription;

    /**
     * @ORM\ManyToOne(targetEntity=Medicine::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $medicine;

    /**
     * @ORM\Column(type="text", options={"comment"="Инструкция по применению"})
     */
    private $instruction;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=ReceptionMethod::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $receptionMethod;

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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    /**
     * @param string $instruction
     * @return $this
     */
    public function setInstruction(string $instruction): self
    {
        $this->instruction = $instruction;

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
     * @return Medicine|null
     */
    public function getMedicine(): ?Medicine
    {
        return $this->medicine;
    }

    /**
     * @param Medicine|null $medicine
     * @return $this
     */
    public function setMedicine(?Medicine $medicine): self
    {
        $this->medicine = $medicine;

        return $this;
    }

    /**
     * @return ReceptionMethod|null
     */
    public function getReceptionMethod(): ?ReceptionMethod
    {
        return $this->receptionMethod;
    }

    /**
     * @param ReceptionMethod|null $receptionMethod
     * @return $this
     */
    public function setReceptionMethod(?ReceptionMethod $receptionMethod): self
    {
        $this->receptionMethod = $receptionMethod;

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