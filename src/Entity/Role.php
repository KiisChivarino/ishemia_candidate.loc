<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Роль
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ роли"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название роли"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"comment"="Техническое название"})
     */
    private $tech_name;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Описание роли"})
     */
    private $description;

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
    public function getTechName(): ?string
    {
        return $this->tech_name;
    }

    /**
     * @param string|null $tech_name
     *
     * @return $this
     */
    public function setTechName(?string $tech_name): self
    {
        $this->tech_name = $tech_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
