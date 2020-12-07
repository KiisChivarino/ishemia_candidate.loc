<?php

namespace App\Entity;

use App\Repository\StartingPointRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StartingPointRepository::class)
 * @ORM\Table(options={"comment":"Точка отсчета"});
 */
class StartingPoint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Имя свойства для точки отсчета добавления обследований по плану"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Заголовок точки отсчета добавления обследований по плану"})
     */
    private $title;

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
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
