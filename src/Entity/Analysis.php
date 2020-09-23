<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Анализ
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisRepository")
 */
class Analysis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ анализа"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название анализа"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="Описание анализа"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnalysisGroup", inversedBy="analyses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysisGroup;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AnalysisRate", mappedBy="analysis", orphanRemoval=true)
     */
    private $analysisRates;

    public function __construct()
    {
        $this->analysisRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAnalysisGroup(): ?AnalysisGroup
    {
        return $this->analysisGroup;
    }

    public function setAnalysisGroup(?AnalysisGroup $analysisGroup): self
    {
        $this->analysisGroup = $analysisGroup;

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
     * @return Collection|AnalysisRate[]
     */
    public function getAnalysisRates(): Collection
    {
        return $this->analysisRates;
    }

    public function addAnalysisRate(AnalysisRate $analysisRate): self
    {
        if (!$this->analysisRates->contains($analysisRate)) {
            $this->analysisRates[] = $analysisRate;
            $analysisRate->setAnalysis($this);
        }

        return $this;
    }

    public function removeAnalysisRate(AnalysisRate $analysisRate): self
    {
        if ($this->analysisRates->contains($analysisRate)) {
            $this->analysisRates->removeElement($analysisRate);
            // set the owning side to null (unless already changed)
            if ($analysisRate->getAnalysis() === $this) {
                $analysisRate->setAnalysis(null);
            }
        }

        return $this;
    }
}
