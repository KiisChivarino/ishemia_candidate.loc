<?php

namespace App\Entity;

use App\Repository\PatientMedicineRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientMedicineRepository::class)
 * @ORM\Table(options={"comment":"Прием лекарства пациентом"});
 */
class PatientMedicine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ лекарства по назначению"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="patientMedicines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalHistory;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название лекарства"})
     */
    private $medicineName;

    /**
     * @ORM\Column(type="text", options={"comment"="Инструкция по применению"})
     */
    private $instruction;

    /**
     * @ORM\Column(type="date", options={"comment"="Планируемая дата начала приема лекарства"})
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return DateTimeInterface|null
     */
    public function getDateBegin(): ?DateTimeInterface
    {
        return $this->dateBegin;
    }

    /**
     * @param DateTimeInterface $dateBegin
     * @return $this
     */
    public function setDateBegin(DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;
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
}
