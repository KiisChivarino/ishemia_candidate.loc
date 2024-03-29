<?php

namespace App\Entity;

use App\Repository\SMSNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * SMS Notification
 * @ORM\Entity(repositoryClass=SMSNotificationRepository::class)
 * @ORM\Table(options={"comment":"SMS уведомления"});
 */
class SMSNotification
{
    /** @var int Первая попытка */
    const FIRST_ATTEMPT = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ sms уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="ID sms сообщения на стороне провайдера"})
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Телефон получателя sms сообщения"})
     */
    private $recipientPhone;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Статус доставки sms сообщения"})
     */
    private $status;

    /**
     * @ORM\Column(type="integer", options={"comment"="Кол-во попыток отправки сообщения"})
     */
    private $attemptCount;

    /**
    * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="smsNotification", cascade={"persist", "remove"})
    */
    private $notification;

    /**
     * @ORM\ManyToOne(targetEntity=ChannelType::class, inversedBy="smsNotification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelType;

    /**
     * SMSNotification constructor.
     */
    public function __construct()
    {
        $this->attemptCount = self::FIRST_ATTEMPT;
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
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return $this
     */
    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttemptCount(): ?int
    {
        return $this->attemptCount;
    }

    /**
     * @param int $attemptCount
     * @return $this
     */
    public function setAttemptCount(int $attemptCount): self
    {
        $this->attemptCount = $attemptCount;

        return $this;
    }

    /**
     * @return Notification|null
     */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification|null $notification
     * @return $this
     */
    public function setNotification(?Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipientPhone(): ?string
    {
        return $this->recipientPhone;
    }

    /**
     * @param string $recipientPhone
     * @return $this
     */
    public function setRecipientPhone(string $recipientPhone): self
    {
        $this->recipientPhone = $recipientPhone;

        return $this;
    }

    /**
     * @return ChannelType|null
     */
    public function getChannelType(): ?ChannelType
    {
        return $this->channelType;
    }

    /**
     * @param ChannelType|null $channelType
     * @return $this
     */
    public function setChannelType(?ChannelType $channelType): self
    {
        $this->channelType = $channelType;

        return $this;
    }
}
