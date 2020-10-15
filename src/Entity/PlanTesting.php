<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PlanTesting
 * @ORM\Entity(repositoryClass="App\Repository\PlanTestingRepository")
 * @ORM\Table(options={"comment":"План обследований"});
 *
 * @package App\Entity
 */
class PlanTesting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ анализа по плану"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnalysisGroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysisGroup;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $timeRange;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeRangeCount;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTimeRange(): ?string
    {
        return $this->timeRange;
    }

    public function setTimeRange(string $timeRange): self
    {
        $this->timeRange = $timeRange;

        return $this;
    }

    public function getTimeRangeCount(): ?int
    {
        return $this->timeRangeCount;
    }

    public function setTimeRangeCount(int $timeRangeCount): self
    {
        $this->timeRangeCount = $timeRangeCount;

        return $this;
    }
}
