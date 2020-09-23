<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Единица измерения
 * @ORM\Entity(repositoryClass="App\Repository\MeasureRepository")
 */
class Measure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ единицы измерения"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, options={"comment"="Русское название единицы измерения"})
     */
    private $nameRu;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"comment"="Английское название единицы измерения"})
     */
    private $nameEn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, options={"comment"="Описание единицы измерения"})
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNameRu(): ?string
    {
        return $this->nameRu;
    }

    public function setNameRu(string $nameRu): self
    {
        $this->nameRu = $nameRu;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
}
