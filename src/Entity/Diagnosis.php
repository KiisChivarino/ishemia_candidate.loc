<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Диагноз
 * @ORM\Entity(repositoryClass="App\Repository\DiagnosisRepository")
 * @ORM\Table(options={"comment":"Диагнозы"});
 */
class Diagnosis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ диагноза"})
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    /**
     * @param string|null $parentCode
     *
     * @return $this
     */
    public function setParentCode(?string $parentCode): self
    {
        $this->parentCode = $parentCode;
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
}
