<?php

namespace App\Entity;

use App\Repository\NotificationTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationTemplateRepository::class)
 */
class NotificationTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="notificationTemplate")
     */
    private $notification;

    /**
     * @ORM\ManyToOne(targetEntity=NotificationReceiverType::class, inversedBy="notificationTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notificationReceiverType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    public function __construct()
    {
        $this->notification = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $notification->setNotificationTemplate($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notification->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getNotificationTemplate() === $this) {
                $notification->setNotificationTemplate(null);
            }
        }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
