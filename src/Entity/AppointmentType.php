<?php

namespace App\Entity;

use App\Repository\AppointmentTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppointmentType
 * @ORM\Entity(repositoryClass=AppointmentTypeRepository::class)
 * @ORM\Table(options={"comment":"Вид приема"});
 *
 * @package App\Entity
 */
class AppointmentType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ вида приема"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Наименование вида приема"})
     */
    private $name;

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
