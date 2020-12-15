<?php

namespace App\Entity;

use App\Repository\EmailNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailNotificationRepository::class)
 */
class EmailNotification
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
    private $emailTo;

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
     * @return string|null
     */
    public function getEmailTo(): ?string
    {
        return $this->emailTo;
    }

    /**
     * @param string $emailTo
     * @return $this
     */
    public function setEmailTo(string $emailTo): self
    {
        $this->emailTo = $emailTo;

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
