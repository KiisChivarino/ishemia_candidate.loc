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
     * @ORM\Column(type="integer")
     */
    private $timeRangeCount;

    /**
     * @ORM\ManyToOne(targetEntity=TimeRange::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeRange;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AnalysisGroup|null
     */
    public function getAnalysisGroup(): ?AnalysisGroup
    {
        return $this->analysisGroup;
    }

    /**
     * @param AnalysisGroup|null $analysisGroup
     *
     * @return $this
     */
    public function setAnalysisGroup(?AnalysisGroup $analysisGroup): self
    {
        $this->analysisGroup = $analysisGroup;
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
     * @return int|null
     */
    public function getTimeRangeCount(): ?int
    {
        return $this->timeRangeCount;
    }

    /**
     * @param int $timeRangeCount
     *
     * @return $this
     */
    public function setTimeRangeCount(int $timeRangeCount): self
    {
        $this->timeRangeCount = $timeRangeCount;
        return $this;
    }

    /**
     * @return TimeRange|null
     */
    public function getTimeRange(): ?TimeRange
    {
        return $this->timeRange;
    }

    /**
     * @param TimeRange|null $timeRange
     *
     * @return $this
     */
    public function setTimeRange(?TimeRange $timeRange): self
    {
        $this->timeRange = $timeRange;
        return $this;
    }
}
