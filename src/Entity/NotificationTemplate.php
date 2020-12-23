<?php

namespace App\Entity;

use App\Repository\NotificationTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationTemplate
 * @ORM\Entity(repositoryClass=NotificationTemplateRepository::class)
 * @ORM\Table(options={"comment":"Шаблон уведомления"});
 */
class NotificationTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ шаблона уведомления"})
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
     * @ORM\Column(type="string", length=255, options={"comment"="Название шаблона уведомления"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", options={"comment"="Текст шаблона уведомления"})
     */
    private $text;

    /**
     * NotificationTemplate constructor.
     */
    public function __construct()
    {
        $this->notification = new ArrayCollection();
    }

    /**
     * @return int|null
     */
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

    /**
     * @param Notification $notification
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notification->contains($notification)) {
            $this->notification[] = $notification;
            $notification->setNotificationTemplate($this);
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
            if ($notification->getNotificationTemplate() === $this) {
                $notification->setNotificationTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return NotificationReceiverType|null
     */
    public function getNotificationReceiverType(): ?NotificationReceiverType
    {
        return $this->notificationReceiverType;
    }

    /**
     * @param NotificationReceiverType|null $notificationReceiverType
     * @return $this
     */
    public function setNotificationReceiverType(?NotificationReceiverType $notificationReceiverType): self
    {
        $this->notificationReceiverType = $notificationReceiverType;

        return $this;
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
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
