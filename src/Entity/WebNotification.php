<?php

namespace App\Entity;

use App\Repository\WebNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Web Notification
 * @ORM\Entity(repositoryClass=WebNotificationRepository::class)
 * @ORM\Table(options={"comment":"Web Уведомления"});
 */
class WebNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ web уведомления"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="webNotification", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $notification;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Информация о получателе"})
     */
    private $receiverString;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Статус просмотра уведомления пользователем"})
     */
    private $isRead;

    /**
     * @ORM\ManyToOne(targetEntity=ChannelType::class, inversedBy="webNotification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelType;

    public function __construct()
    {
        $this->isRead = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Notification|null
     */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function setNotification(Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReceiverString(): ?string
    {
        return $this->receiverString;
    }

    /**
     * @param string $receiverString
     * @return $this
     */
    public function setReceiverString(string $receiverString): self
    {
        $this->receiverString = $receiverString;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     * @return $this
     */
    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * @return ChannelType
     */
    public function getChannelType(): ChannelType
    {
        return $this->channelType;
    }

    /**
     * @param ChannelType $channelType
     * @return $this
     */
    public function setChannelType(ChannelType $channelType): self
    {
        $this->channelType = $channelType;

        return $this;
    }
}
