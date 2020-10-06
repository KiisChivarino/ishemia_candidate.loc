<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Предельные нормальные значения анализа
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisRateRepository")
 * @Table(name="analysis_rate",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="analysis_rate_unique",
 *            columns={"analysis_id", "measure_id", "gender_id"})
 *    }
 * )
 */
class AnalysisRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ нормальных значений"})
     */
    private $id;

    /**
     * @ORM\Column(type="float", options={"comment"="Минимальное нормальное значение"})
     */
    private $rateMin;

    /**
     * @ORM\Column(type="float", options={"comment"="Максимальное нормальное значение"})
     */
    private $rateMax;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Analysis", inversedBy="analysisRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysis;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Measure")
     * @ORM\JoinColumn(nullable=false)
     */
    private $measure;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=Gender::class)
     */
    private $gender;

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
     * @return float|null
     */
    public function getRateMin(): ?float
    {
        return $this->rateMin;
    }

    /**
     * @param float $rateMin
     *
     * @return $this
     */
    public function setRateMin(float $rateMin): self
    {
        $this->rateMin = $rateMin;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRateMax(): ?float
    {
        return $this->rateMax;
    }

    /**
     * @param float $rateMax
     *
     * @return $this
     */
    public function setRateMax(float $rateMax): self
    {
        $this->rateMax = $rateMax;
        return $this;
    }

    /**
     * @return Analysis|null
     */
    public function getAnalysis(): ?Analysis
    {
        return $this->analysis;
    }

    /**
     * @param Analysis|null $analysis
     *
     * @return $this
     */
    public function setAnalysis(?Analysis $analysis): self
    {
        $this->analysis = $analysis;
        return $this;
    }

    /**
     * @return Measure|null
     */
    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    /**
     * @param Measure|null $measure
     *
     * @return $this
     */
    public function setMeasure(?Measure $measure): self
    {
        $this->measure = $measure;
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
     * @return Gender|null
     */
    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    /**
     * @param Gender|null $gender
     *
     * @return $this
     */
    public function setGender(?Gender $gender): self
    {
        $this->gender = $gender;
        return $this;
    }
}
