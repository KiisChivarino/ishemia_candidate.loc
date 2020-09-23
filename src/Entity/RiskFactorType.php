<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Тип фактора риска
 * @ORM\Entity(repositoryClass="App\Repository\RiskFactorTypeRepository")
 * @ORM\Table(options={"comment":"Типы факторов риска"});
 */
class RiskFactorType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ типа фактора риска"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RiskFactor", mappedBy="riskFactorType")
     */
    private $riskFactors;

    /**
     * RiskFactorType constructor.
     */
    public function __construct()
    {
        $this->riskFactors = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
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
     * @return Collection|RiskFactor[]
     */
    public function getRiskFactors(): Collection
    {
        return $this->riskFactors;
    }

    /**
     * @param RiskFactor $riskFactor
     *
     * @return $this
     */
    public function addRiskFactor(RiskFactor $riskFactor): self
    {
        if (!$this->riskFactors->contains($riskFactor)) {
            $this->riskFactors[] = $riskFactor;
            $riskFactor->setRiskFactorType($this);
        }
        return $this;
    }

    /**
     * @param RiskFactor $riskFactor
     *
     * @return $this
     */
    public function removeRiskFactor(RiskFactor $riskFactor): self
    {
        if ($this->riskFactors->contains($riskFactor)) {
            $this->riskFactors->removeElement($riskFactor);
            // set the owning side to null (unless already changed)
            if ($riskFactor->getRiskFactorType() === $this) {
                $riskFactor->setRiskFactorType(null);
            }
        }
        return $this;
    }
}
