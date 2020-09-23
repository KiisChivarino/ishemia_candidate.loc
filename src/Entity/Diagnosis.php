<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Диагноз
 * @ORM\Entity(repositoryClass="App\Repository\DiagnosisRepository")
 */
class Diagnosis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ записи"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256, options={"comment"="Название диагноза"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Код диагноза"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"comment"="Код группы диагнозов (диагноза верхнего уровня)"})
     */
    private $parentCode;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    public function setParentCode(?string $parentCode): self
    {
        $this->parentCode = $parentCode;

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
