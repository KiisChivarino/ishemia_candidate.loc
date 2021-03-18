<?php

namespace App\Entity;

use App\Repository\PlanAppointmentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanAppointmentRepository::class)
 * @ORM\Table(options={"comment":"План приемов"});
 */
class PlanAppointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=TimeRange::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeRange;

    /**
     * @ORM\Column(type="integer", options={"comment"="Срок выполнения"})
     */
    private $timeRangeCount;

    /**
     * @ORM\ManyToOne(targetEntity=StartingPoint::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $startingPoint;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return TimeRange
     */
    public function getTimeRange(): TimeRange
    {
        return $this->timeRange;
    }

    /**
     * @param TimeRange $timeRange
     *
     * @return $this
     */
    public function setTimeRange(TimeRange $timeRange): self
    {
        $this->timeRange = $timeRange;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeRangeCount(): int
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
     * @return StartingPoint|null
     */
    public function getStartingPoint(): ?StartingPoint
    {
        return $this->startingPoint;
    }

    /**
     * @param StartingPoint|null $startingPoint
     * @return $this
     */
    public function setStartingPoint(?StartingPoint $startingPoint): self
    {
        $this->startingPoint = $startingPoint;
        return $this;
    }
}
