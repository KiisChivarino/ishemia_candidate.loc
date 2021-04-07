<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Препарат
 * @ORM\Entity(repositoryClass="App\Repository\MedicineRepository")
 */
class Medicine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ препарата"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название препарата"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", options={"comment"="Описание использования"})
     */
    private $description;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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