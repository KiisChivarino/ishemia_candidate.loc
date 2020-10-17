<?php

namespace App\Entity;

use App\Repository\DateIntervalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateIntervalRepository::class)
 * @ORM\Table(options={"comment":"Интервал даты"});
 */
class DateInterval
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ интервала"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, options={"comment"="Имя интервала"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30, options={"comment"="Заголовок интервала"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1, options={"comment"="Формат интервала"})
     */
    private $format;

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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }
}
