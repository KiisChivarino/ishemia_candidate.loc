<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ОКТМО
 * @ORM\Entity(repositoryClass="App\Repository\OktmoRepository")
 * @ORM\Table(options={"comment":"ОКТМО"});
 */
class Oktmo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ ОКТМО"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $kod;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $kod2;

    /**
     * @ORM\Column(type="integer")
     */
    private $subKod1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $subKod2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $subKod3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $subKod4;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $p1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $p2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $kch;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $name2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="integer")
     */
    private $federalDistrictId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $federalDistrictName;

    /**
     * @ORM\Column(type="integer")
     */
    private $regionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $regionName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $settlementTypeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $settlementTypeName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKod(): ?string
    {
        return $this->kod;
    }

    public function setKod(string $kod): self
    {
        $this->kod = $kod;

        return $this;
    }

    public function getKod2(): ?string
    {
        return $this->kod2;
    }

    public function setKod2(string $kod2): self
    {
        $this->kod2 = $kod2;

        return $this;
    }

    public function getSubKod1(): ?int
    {
        return $this->subKod1;
    }

    public function setSubKod1(int $subKod1): self
    {
        $this->subKod1 = $subKod1;

        return $this;
    }

    public function getSubKod2(): ?int
    {
        return $this->subKod2;
    }

    public function setSubKod2(?int $subKod2): self
    {
        $this->subKod2 = $subKod2;

        return $this;
    }

    public function getSubKod3(): ?int
    {
        return $this->subKod3;
    }

    public function setSubKod3(?int $subKod3): self
    {
        $this->subKod3 = $subKod3;

        return $this;
    }

    public function getSubKod4(): ?int
    {
        return $this->subKod4;
    }

    public function setSubKod4(?int $subKod4): self
    {
        $this->subKod4 = $subKod4;

        return $this;
    }

    public function getP1(): ?int
    {
        return $this->p1;
    }

    public function setP1(?int $p1): self
    {
        $this->p1 = $p1;

        return $this;
    }

    public function getP2(): ?int
    {
        return $this->p2;
    }

    public function setP2(?int $p2): self
    {
        $this->p2 = $p2;

        return $this;
    }

    public function getKch(): ?int
    {
        return $this->kch;
    }

    public function setKch(?int $kch): self
    {
        $this->kch = $kch;

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

    public function getName2(): ?string
    {
        return $this->name2;
    }

    public function setName2(?string $name2): self
    {
        $this->name2 = $name2;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getFederalDistrictId(): ?int
    {
        return $this->federalDistrictId;
    }

    public function setFederalDistrictId(int $federalDistrictId): self
    {
        $this->federalDistrictId = $federalDistrictId;

        return $this;
    }

    public function getFederalDistrictName(): ?string
    {
        return $this->federalDistrictName;
    }

    public function setFederalDistrictName(string $federalDistrictName): self
    {
        $this->federalDistrictName = $federalDistrictName;

        return $this;
    }

    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    public function setRegionId(int $regionId): self
    {
        $this->regionId = $regionId;

        return $this;
    }

    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    public function setRegionName(string $regionName): self
    {
        $this->regionName = $regionName;

        return $this;
    }

    public function getSettlementTypeId(): ?int
    {
        return $this->settlementTypeId;
    }

    public function setSettlementTypeId(?int $settlementTypeId): self
    {
        $this->settlementTypeId = $settlementTypeId;

        return $this;
    }

    public function getSettlementTypeName(): ?string
    {
        return $this->settlementTypeName;
    }

    public function setSettlementTypeName(?string $settlementTypeName): self
    {
        $this->settlementTypeName = $settlementTypeName;

        return $this;
    }
}
