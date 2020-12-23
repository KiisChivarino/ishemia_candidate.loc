<?php

namespace App\Entity;

use App\Repository\ChannelTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChannelTypeRepository::class)
 */
class ChannelType
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
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="channelType")
     */
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity=SMSNotification::class, mappedBy="channelType")
     */
    private $smsNotification;

    /**
     * @ORM\OneToMany(targetEntity=EmailNotification::class, mappedBy="channelType")
     */
    private $emailNotification;

    /**
     * @ORM\OneToMany(targetEntity=WebNotification::class, mappedBy="channelType")
     */
    private $webNotification;

    public function __construct()
    {
        $this->notification = new ArrayCollection();
        $this->smsNotification = new ArrayCollection();
        $this->emailNotification = new ArrayCollection();
        $this->webNotification = new ArrayCollection();
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

    /**
     * @return Collection|Notification[]
     */
    public function getNotification(): Collection
    {
        return $this->notification;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notification->contains($notification)) {
            $this->notification[] = $notification;
            $notification->setChannelType($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notification->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getChannelType() === $this) {
                $notification->setChannelType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SMSNotification[]
     */
    public function getSmsNotification(): Collection
    {
        return $this->smsNotification;
    }

    public function addSmsNotification(SMSNotification $smsNotification): self
    {
        if (!$this->smsNotification->contains($smsNotification)) {
            $this->smsNotification[] = $smsNotification;
            $smsNotification->setChannelType($this);
        }

        return $this;
    }

    public function removeSmsNotification(SMSNotification $smsNotification): self
    {
        if ($this->smsNotification->removeElement($smsNotification)) {
            // set the owning side to null (unless already changed)
            if ($smsNotification->getChannelType() === $this) {
                $smsNotification->setChannelType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EmailNotification[]
     */
    public function getEmailNotification(): Collection
    {
        return $this->emailNotification;
    }

    public function addEmailNotification(EmailNotification $emailNotification): self
    {
        if (!$this->emailNotification->contains($emailNotification)) {
            $this->emailNotification[] = $emailNotification;
            $emailNotification->setChannelType($this);
        }

        return $this;
    }

    public function removeEmailNotification(EmailNotification $emailNotification): self
    {
        if ($this->emailNotification->removeElement($emailNotification)) {
            // set the owning side to null (unless already changed)
            if ($emailNotification->getChannelType() === $this) {
                $emailNotification->setChannelType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WebNotification[]
     */
    public function getWebNotification(): Collection
    {
        return $this->webNotification;
    }

    public function addWebNotification(WebNotification $webNotification): self
    {
        if (!$this->webNotification->contains($webNotification)) {
            $this->webNotification[] = $webNotification;
            $webNotification->setChannelType($this);
        }

        return $this;
    }

    public function removeWebNotification(WebNotification $webNotification): self
    {
        if ($this->webNotification->removeElement($webNotification)) {
            // set the owning side to null (unless already changed)
            if ($webNotification->getChannelType() === $this) {
                $webNotification->setChannelType(null);
            }
        }

        return $this;
    }
}
