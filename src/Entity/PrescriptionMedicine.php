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
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

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
}
