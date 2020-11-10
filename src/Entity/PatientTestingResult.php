<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PatientTestingResult
 * Результаты анализа
 * @ORM\Entity(repositoryClass="App\Repository\PatientTestingResultRepository")
 * @ORM\Table(options={"comment":"Результаты анализа"});
 * @package App\Entity
 */
class PatientTestingResult
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ резултатов анализа"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PatientTesting", inversedBy="patientTestingResults", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientTesting;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnalysisRate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysisRate;

    /**
     * @ORM\Column(type="float", options={"comment"="Результат анализа"}, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=Analysis::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientTesting(): ?PatientTesting
    {
        return $this->patientTesting;
    }

    public function setPatientTesting(?PatientTesting $patientTesting): self
    {
        $this->patientTesting = $patientTesting;

        return $this;
    }

    public function getAnalysisRate(): ?AnalysisRate
    {
        return $this->analysisRate;
    }

    public function setAnalysisRate(?AnalysisRate $analysisRate): self
    {
        $this->analysisRate = $analysisRate;

        return $this;
    }

    public function getResult(): ?float
    {
        return $this->result;
    }

    public function setResult(?float $result): self
    {
        $this->result = $result;

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

    public function getAnalysis(): ?Analysis
    {
        return $this->analysis;
    }

    public function setAnalysis(?Analysis $analysis): self
    {
        $this->analysis = $analysis;

        return $this;
    }
}
