<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Район
 * @ORM\Entity(repositoryClass="App\Repository\DistrictRepository")
 * @ORM\Table(options={"comment":"Районы"});
 */
class District
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ района"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, options={"comment"="Название района"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region", inversedBy="districts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Patient", mappedBy="district")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="district")
     */
    private $cities;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Oktmo", cascade={"persist", "remove"})
     */
    private $oktmo;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

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
            $patient->setDistrict($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getDistrict() === $this) {
                $patient->setDistrict(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setDistrict($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getDistrict() === $this) {
                $city->setDistrict(null);
            }
        }

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
