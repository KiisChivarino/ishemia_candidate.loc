<?php

namespace App\Entity;

use App\Repository\ReceptionMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReceptionMethodRepository::class)
 * @ORM\Table(options={"comment":"Способ приема препарата"});
 */
class ReceptionMethod
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ способа приема препарата"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название способа приема"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=PatientMedicine::class, mappedBy="receptionMethod")
     */
    private $patientMedicines;

    /**
     * ReceptionMethod constructor.
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
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
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
            $patientMedicine->setReceptionMethod($this);
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
            if ($patientMedicine->getReceptionMethod() === $this) {
                $patientMedicine->setReceptionMethod(null);
            }
        }
        return $this;
    }
}
