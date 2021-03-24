<?php

namespace App\Entity;

use App\Repository\NotificationReceiverTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification Receiver Type
 * @ORM\Entity(repositoryClass=NotificationReceiverTypeRepository::class)
 * @ORM\Table(options={"comment":"Тип получателя уведомления"});
 */
class NotificationReceiverType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ типа получателя уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название типа получателя уведомления"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="notificationReceiverType")
     */
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity=NotificationTemplate::class, mappedBy="notificationReceiverType")
     */
    private $notificationTemplates;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * NotificationReceiverType constructor.
     */
    public function __construct()
    {
        $this->notification = new ArrayCollection();
        $this->notificationTemplates = new ArrayCollection();
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
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

    /**
     * @param Notification $notification
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notification->contains($notification)) {
            $this->notification[] = $notification;
            $notification->setNotificationReceiverType($this);
        }

        return $this;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function removeNotification(Notification $notification): self
    {
        if ($this->notification->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getNotificationReceiverType() === $this) {
                $notification->setNotificationReceiverType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NotificationTemplate[]
     */
    public function getNotificationTemplates(): Collection
    {
        return $this->notificationTemplates;
    }

    /**
     * @param NotificationTemplate $notificationTemplate
     * @return $this
     */
    public function addNotificationTemplate(NotificationTemplate $notificationTemplate): self
    {
        if (!$this->notificationTemplates->contains($notificationTemplate)) {
            $this->notificationTemplates[] = $notificationTemplate;
            $notificationTemplate->setNotificationReceiverType($this);
        }

        return $this;
    }

    /**
     * @param NotificationTemplate $notificationTemplate
     * @return $this
     */
    public function removeNotificationTemplate(NotificationTemplate $notificationTemplate): self
    {
        if ($this->notificationTemplates->removeElement($notificationTemplate)) {
            // set the owning side to null (unless already changed)
            if ($notificationTemplate->getNotificationReceiverType() === $this) {
                $notificationTemplate->setNotificationReceiverType(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
