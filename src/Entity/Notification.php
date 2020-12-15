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
     * @ORM\Column(type="string", length=255)
     */
    private $notificationType;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=AuthUser::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $from;

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
    public function getNotificationTime(): ?\DateTimeInterface
    {
        return $this->notificationTime;
    }

    /**
     * @param DateTimeInterface $notificationTime
     * @return $this
     */
    public function setNotificationTime(\DateTimeInterface $notificationTime): self
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
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return AuthUser|null
     */
    public function getFrom(): ?AuthUser
    {
        return $this->from;
    }

    /**
     * @param AuthUser|null $from
     * @return $this
     */
    public function setFrom(?AuthUser $from): self
    {
        $this->from = $from;

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
}
