<?php

namespace App\Entity;

use App\Repository\PatientMedicineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Лекарство пациента
 * @ORM\Entity(repositoryClass=PatientMedicineRepository::class)
 */
class PatientMedicine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ лекарство пациента"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название лекарства"})
     */
    private $medicineName;

    /**
     * @ORM\Column(type="text", options={"comment"="Инструкция к применению лекарства"})
     */
    private $instruction;

    /**
     * @ORM\ManyToOne(targetEntity=ReceptionMethod::class, inversedBy="patientMedicines")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receptionMethod;

    /**
     * @ORM\ManyToOne(targetEntity=Medicine::class, inversedBy="patientMedicines")
     * @ORM\JoinColumn(nullable=true)
     */
    private $medicine;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToOne(targetEntity=PrescriptionMedicine::class, mappedBy="patientMedicine", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $prescriptionMedicine;

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
    public function getMedicineName(): ?string
    {
        return $this->medicineName;
    }

    /**
     * @param string $medicineName
     * @return $this
     */
    public function setMedicineName(string $medicineName): self
    {
        $this->medicineName = $medicineName;

        return $this;
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

    public function getPrescriptionMedicine(): ?PrescriptionMedicine
    {
        return $this->prescriptionMedicine;
    }

    public function setPrescriptionMedicine(PrescriptionMedicine $prescriptionMedicine): self
    {
        $this->prescriptionMedicine = $prescriptionMedicine;

        return $this;
    }
}
