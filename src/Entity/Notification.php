<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\Table(options={"comment":"Уведомление"});
 * @package App\Entity
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Тип уведомления"})
     */
    private $notificationType;

    /**
     * @ORM\ManyToOne(targetEntity=AuthUser::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $authUserSender;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время создания уведомления"})
     */
    private $notificationTime;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Текст уведомления"})
     */
    private $text;

    /**
     * @ORM\OneToOne(targetEntity=SMSNotification::class, mappedBy="notification", cascade={"persist", "remove"})
     */
    private $smsNotification;

    /**
     * @ORM\OneToOne(targetEntity=EmailNotification::class, mappedBy="notification", cascade={"persist", "remove"})
     */
    private $emailNotification;

    /**
     * @ORM\OneToOne(targetEntity=PatientNotification::class, mappedBy="notification", cascade={"persist", "remove"})
     */
    private $patientNotification;

    /**
     * @ORM\ManyToOne(targetEntity=NotificationReceiverType::class, inversedBy="notification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notificationReceiverType;

    /**
     * @ORM\ManyToOne(targetEntity=NotificationTemplate::class, inversedBy="notification")
     */
    private $notificationTemplate;

    /**
     * @ORM\OneToOne(targetEntity=WebNotification::class, mappedBy="notification", cascade={"persist", "remove"})
     */
    private $webNotification;

    /**
     * @ORM\ManyToOne(targetEntity=ChannelType::class, inversedBy="notification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelType;

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
    public function getNotificationType(): ?string
    {
        return $this->notificationType;
    }

    /**
     * @param string $notificationType
     * @return $this
     */
    public function setNotificationType(string $notificationType): self
    {
        $this->notificationType = $notificationType;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getNotificationTime(): ?DateTimeInterface
    {
        return $this->notificationTime;
    }

    /**
     * @param DateTimeInterface $notificationTime
     * @return $this
     */
    public function setNotificationTime(DateTimeInterface $notificationTime): self
    {
        $this->notificationTime = $notificationTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return $this
     */
    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return SMSNotification|null
     */
    public function getSmsNotification(): ?SMSNotification
    {
        return $this->smsNotification;
    }

    /**
     * @param SMSNotification|null $smsNotification
     * @return $this
     */
    public function setSmsNotification(?SMSNotification $smsNotification): self
    {
        // unset the owning side of the relation if necessary
        if ($smsNotification === null && $this->smsNotification !== null) {
            $this->smsNotification->setNotification(null);
        }
        // set the owning side of the relation if necessary
        if ($smsNotification !== null && $smsNotification->getNotification() !== $this) {
            $smsNotification->setNotification($this);
        }
        $this->smsNotification = $smsNotification;
        return $this;
    }

    /**
     * @return EmailNotification|null
     */
    public function getEmailNotification(): ?EmailNotification
    {
        return $this->emailNotification;
    }

    /**
     * @param EmailNotification|null $emailNotification
     * @return $this
     */
    public function setEmailNotification(?EmailNotification $emailNotification): self
    {
        // unset the owning side of the relation if necessary
        if ($emailNotification === null && $this->emailNotification !== null) {
            $this->emailNotification->setNotification(null);
        }
        // set the owning side of the relation if necessary
        if ($emailNotification !== null && $emailNotification->getNotification() !== $this) {
            $emailNotification->setNotification($this);
        }
        $this->emailNotification = $emailNotification;
        return $this;
    }

    /**
     * @return AuthUser|null
     */
    public function getAuthUserSender(): ?AuthUser
    {
        return $this->authUserSender;
    }

    /**
     * @param AuthUser|null $authUserSender
     * @return $this
     */
    public function setAuthUserSender(?AuthUser $authUserSender): self
    {
        $this->authUserSender = $authUserSender;
        return $this;
    }

    public function getPatientNotification(): ?PatientNotification
    {
        return $this->patientNotification;
    }

    public function setPatientNotification(PatientNotification $patientNotification): self
    {
        // set the owning side of the relation if necessary
        if ($patientNotification->getNotification() !== $this) {
            $patientNotification->setNotification($this);
        }

        $this->patientNotification = $patientNotification;

        return $this;
    }

    public function getNotificationReceiverType(): ?NotificationReceiverType
    {
        return $this->notificationReceiverType;
    }

    public function setNotificationReceiverType(?NotificationReceiverType $notificationReceiverType): self
    {
        $this->notificationReceiverType = $notificationReceiverType;

        return $this;
    }

    public function getNotificationTemplate(): ?NotificationTemplate
    {
        return $this->notificationTemplate;
    }

    public function setNotificationTemplate(?NotificationTemplate $notificationTemplate): self
    {
        $this->notificationTemplate = $notificationTemplate;

        return $this;
    }

    public function getWebNotification(): ?WebNotification
    {
        return $this->webNotification;
    }

    public function setWebNotification(WebNotification $webNotification): self
    {
        // set the owning side of the relation if necessary
        if ($webNotification->getNotification() !== $this) {
            $webNotification->setNotification($this);
        }

        $this->webNotification = $webNotification;

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
