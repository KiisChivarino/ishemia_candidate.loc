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
    /** @var int Перввая попытка */
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
    private $smsTo;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Статус доставки sms сообщения"})
     */
    private $status;

    /**
     * @ORM\Column(type="integer", options={"comment"="Кол-во попыток отправки сообщения"})
     */
    private $attempt;

    /**
    * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="smsNotification", cascade={"persist", "remove"})
    */
    private $notification;

    public function __construct()
    {
        $this->attempt = self::FIRST_ATTEMPT;
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
    public function getSmsTo(): ?string
    {
        return $this->smsTo;
    }

    /**
     * @param string $smsTo
     * @return $this
     */
    public function setSmsTo(string $smsTo): self
    {
        $this->smsTo = $smsTo;

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
    public function getAttempt(): ?int
    {
        return $this->attempt;
    }

    /**
     * @param int $attempt
     * @return $this
     */
    public function setAttempt(int $attempt): self
    {
        $this->attempt = $attempt;

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
}
