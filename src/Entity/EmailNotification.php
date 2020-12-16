<?php

namespace App\Entity;

use App\Repository\EmailNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Notification
 * @ORM\Entity(repositoryClass=EmailNotificationRepository::class)
 * @ORM\Table(options={"comment":"Email уведомления"});
 */
class EmailNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ email уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Email получателя"})
     */
    private $patientRecipientEmail;

    /**
     * @ORM\OneToOne(targetEntity=Notification::class, inversedBy="emailNotification", cascade={"persist", "remove"})
     */
    private $notification;

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
    public function getPatientRecipientEmail(): ?string
    {
        return $this->patientRecipientEmail;
    }

    /**
     * @param string $patientRecipientEmail
     * @return $this
     */
    public function setPatientRecipientEmail(string $patientRecipientEmail): self
    {
        $this->patientRecipientEmail = $patientRecipientEmail;

        return $this;
    }
}
