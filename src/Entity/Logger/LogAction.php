<?php

namespace App\Entity\Logger;

use App\Entity\Logger\Log;
use App\Repository\LogActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogActionRepository::class)
 */
class LogAction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = 1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Logger\Log", mappedBy="action")
     */
    private $log;

    public function __construct()
    {
        $this->log = new ArrayCollection();
    }

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLog(): Collection
    {
        return $this->log;
    }

    public function addLog(Log $log): self
    {
        if (!$this->log->contains($log)) {
            $this->log[] = $log;
            $log->setAction($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->log->contains($log)) {
            $this->log->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getAction() === $this) {
                $log->setAction(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
