<?php

namespace App\Entity;

use App\Repository\ComplaintRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ComplaintRepository::class)
 * @ORM\Table(options={"comment":"Жалоба"});
 */
class Complaint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название жалобы"})
     *
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Название жалобы должно содержать более {{ limit }} символов",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Описание жалобы"})
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

    public function setDescription(?string $description): self
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
