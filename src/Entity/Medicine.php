<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Препарат
 * @ORM\Entity(repositoryClass="App\Repository\MedicineRepository")
 */
class Medicine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ препарата"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название препарата"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", options={"comment"="Описание использования"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=PatientMedicine::class, mappedBy="medicine")
     * @ORM\JoinColumn(nullable=true)
     */
    private $patientMedicines;

    /**
     * Medicine constructor.
     */
    public function __construct()
    {
        $this->patientMedicines = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * @return Collection|PatientMedicine[]
     */
    public function getPatientMedicines(): Collection
    {
        return $this->patientMedicines;
    }

    /**
     * @param PatientMedicine $patientMedicine
     * @return $this
     */
    public function addPatientMedicine(PatientMedicine $patientMedicine): self
    {
        if (!$this->patientMedicines->contains($patientMedicine)) {
            $this->patientMedicines[] = $patientMedicine;
            $patientMedicine->setMedicine($this);
        }

        return $this;
    }

    /**
     * @param PatientMedicine $patientMedicine
     * @return $this
     */
    public function removePatientMedicine(PatientMedicine $patientMedicine): self
    {
        if ($this->patientMedicines->removeElement($patientMedicine)) {
            // set the owning side to null (unless already changed)
            if ($patientMedicine->getMedicine() === $this) {
                $patientMedicine->setMedicine(null);
            }
        }

        return $this;
    }
}
