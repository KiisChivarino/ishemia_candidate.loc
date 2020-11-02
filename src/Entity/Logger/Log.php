<?php

namespace App\Entity\Logger;

use App\Entity\Logger\LogAction;
use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Logger\LogAction", inversedBy="log", cascade={"persist"})
     * @ORM\JoinColumn(name="log_action_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userString;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAction(): ?LogAction
    {
        return $this->action;
    }

    public function setAction(?LogAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getUserString(): ?string
    {
        return $this->userString;
    }

    public function setUserString(string $userString): self
    {
        $this->userString = $userString;

        return $this;
    }


}
