<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Фактор риска
 * @ORM\Entity(repositoryClass="App\Repository\RiskFactorRepository")
 * @ORM\Table(options={"comment":"Факторы риска"});
 */
class RiskFactor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ фактора риска"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", options={"comment"="Количество баллов"})
     */
    private $scores;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RiskFactorType", inversedBy="riskFactors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $riskFactorType;

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
     * @return int|null
     */
    public function getScores(): ?int
    {
        return $this->scores;
    }

    /**
     * @param int $scores
     *
     * @return $this
     */
    public function setScores(int $scores): self
    {
        $this->scores = $scores;
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
     * @return RiskFactorType|null
     */
    public function getRiskFactorType(): ?RiskFactorType
    {
        return $this->riskFactorType;
    }

    /**
     * @param RiskFactorType|null $riskFactorType
     *
     * @return $this
     */
    public function setRiskFactorType(?RiskFactorType $riskFactorType): self
    {
        $this->riskFactorType = $riskFactorType;
        return $this;
    }
}
