<?php

namespace App\Entity\Logger;

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
    private $enabled = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Logger\Log", mappedBy="action")
     */
    private $log;

    public function __construct()
    {
        $this->log = new ArrayCollection();
    }

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
     * @return Collection|Log[]
     */
    public function getLog(): Collection
    {
        return $this->log;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function addLog(Log $log): self
    {
        if (!$this->log->contains($log)) {
            $this->log[] = $log;
            $log->setAction($this);
        }
        return $this;
    }

    /**
     * @param Log $log
     * @return $this
     */
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

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getName();
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
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }
}
