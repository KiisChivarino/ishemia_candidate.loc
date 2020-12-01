<?php

namespace App\Entity;

use App\Repository\TimeRangeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeRangeRepository::class)
 * @ORM\Table(options={"comment":"Временной диапазон"});
 */
class TimeRange
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, nullable=true, options={"comment"="Заголовок временного диапазона"})
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\Column(type="integer", options={"comment"="Множитель", "default"=1})
     */
    private $multiplier;

    /**
     * @ORM\ManyToOne(targetEntity=DateInterval::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $dateInterval;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг регулярный период", "default"=false})
     */
    private $isRegular;

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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
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
    public function getMultiplier(): ?int
    {
        return $this->multiplier;
    }

    /**
     * @param int $multiplier
     *
     * @return $this
     */
    public function setMultiplier(int $multiplier): self
    {
        $this->multiplier = $multiplier;
        return $this;
    }

    /**
     * @return DateInterval|null
     */
    public function getDateInterval(): ?DateInterval
    {
        return $this->dateInterval;
    }

    /**
     * @param DateInterval|null $dateInterval
     *
     * @return $this
     */
    public function setDateInterval(?DateInterval $dateInterval): self
    {
        $this->dateInterval = $dateInterval;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsRegular(): ?bool
    {
        return $this->isRegular;
    }

    /**
     * @param bool $isRegular
     * @return $this
     */
    public function setIsRegular(bool $isRegular): self
    {
        $this->isRegular = $isRegular;
        return $this;
    }
}
