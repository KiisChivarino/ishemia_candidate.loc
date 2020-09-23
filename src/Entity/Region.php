<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RegionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Регион
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ региона"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название региона"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=8, options={"comment"="Номер региона"}, nullable=true)
     */
    private $region_number;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="region", orphanRemoval=true)
     */
    private $cities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hospital", mappedBy="region", orphanRemoval=true)
     */
    private $hospitals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\District", mappedBy="region", orphanRemoval=true)
     */
    private $districts;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oktmoRegionId;

    /**
     * Region constructor.
     */
    public function __construct()
    {
        $this->city = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->hospitals = new ArrayCollection();
        $this->districts = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     *
     * @return $this
     */
    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
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
     * @return string|null
     */
    public function getRegionNumber(): ?string
    {
        return $this->region_number;
    }

    /**
     * @param string|null $region_number
     *
     * @return $this
     */
    public function setRegionNumber(?string $region_number): self
    {
        $this->region_number = $region_number;

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
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setRegion($this);
        }
        return $this;
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getRegion() === $this) {
                $city->setRegion(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Hospital[]
     */
    public function getHospitals(): Collection
    {
        return $this->hospitals;
    }

    /**
     * @param Hospital $hospital
     *
     * @return $this
     */
    public function addHospital(Hospital $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals[] = $hospital;
            $hospital->setRegion($this);
        }
        return $this;
    }

    /**
     * @param Hospital $hospital
     *
     * @return $this
     */
    public function removeHospital(Hospital $hospital): self
    {
        if ($this->hospitals->contains($hospital)) {
            $this->hospitals->removeElement($hospital);
            // set the owning side to null (unless already changed)
            if ($hospital->getRegion() === $this) {
                $hospital->setRegion(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|District[]
     */
    public function getDistricts(): Collection
    {
        return $this->districts;
    }

    /**
     * @param District $district
     *
     * @return $this
     */
    public function addDistrict(District $district): self
    {
        if (!$this->districts->contains($district)) {
            $this->districts[] = $district;
            $district->setRegion($this);
        }
        return $this;
    }

    /**
     * @param District $district
     *
     * @return $this
     */
    public function removeDistrict(District $district): self
    {
        if ($this->districts->contains($district)) {
            $this->districts->removeElement($district);
            // set the owning side to null (unless already changed)
            if ($district->getRegion() === $this) {
                $district->setRegion(null);
            }
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOktmoRegionId(): ?int
    {
        return $this->oktmoRegionId;
    }

    /**
     * @param int|null $oktmoRegionId
     *
     * @return $this
     */
    public function setOktmoRegionId(?int $oktmoRegionId): self
    {
        $this->oktmoRegionId = $oktmoRegionId;
        return $this;
    }
}
