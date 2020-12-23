<?php

namespace App\Entity;

use App\Repository\WebNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WebNotificationRepository::class)
 */
class WebNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="webNotification", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $notification;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $receiverString;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    /**
     * @ORM\ManyToOne(targetEntity=ChannelType::class, inversedBy="webNotification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getReceiverString(): ?string
    {
        return $this->receiverString;
    }

    public function setReceiverString(string $receiverString): self
    {
        $this->receiverString = $receiverString;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getChannelType(): ?ChannelType
    {
        return $this->channelType;
    }

    public function setChannelType(?ChannelType $channelType): self
    {
        $this->channelType = $channelType;

        return $this;
    }
}
