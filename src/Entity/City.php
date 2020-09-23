<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Город
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ города"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название города"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hospital", mappedBy="city", orphanRemoval=true)
     */
    private $hospitals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Patient", mappedBy="city")
     */
    private $patients;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\District", inversedBy="cities")
     */
    private $district;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Oktmo", cascade={"persist", "remove"})
     */
    private $oktmo;

    public function __construct()
    {
        $this->hospitals = new ArrayCollection();
        $this->patients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|Hospital[]
     */
    public function getHospitals(): Collection
    {
        return $this->hospitals;
    }

    public function addHospital(Hospital $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals[] = $hospital;
            $hospital->setCity($this);
        }

        return $this;
    }

    public function removeHospital(Hospital $hospital): self
    {
        if ($this->hospitals->contains($hospital)) {
            $this->hospitals->removeElement($hospital);
            // set the owning side to null (unless already changed)
            if ($hospital->getCity() === $this) {
                $hospital->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setCity($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getCity() === $this) {
                $patient->setCity(null);
            }
        }

        return $this;
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getOktmo(): ?Oktmo
    {
        return $this->oktmo;
    }

    public function setOktmo(?Oktmo $oktmo): self
    {
        $this->oktmo = $oktmo;

        return $this;
    }
}
