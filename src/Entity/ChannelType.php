<?php

namespace App\Entity;

use App\Repository\ChannelTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Channel Type
 * @ORM\Entity(repositoryClass=ChannelTypeRepository::class)
 * @ORM\Table(options={"comment":"Тип канала"});
 */
class ChannelType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ типа канала"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название типа канала"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="channelType")
     */
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity=SMSNotification::class, mappedBy="channelType")
     */
    private $smsNotification;

    /**
     * @ORM\OneToMany(targetEntity=EmailNotification::class, mappedBy="channelType")
     */
    private $emailNotification;

    /**
     * @ORM\OneToMany(targetEntity=WebNotification::class, mappedBy="channelType")
     */
    private $webNotification;

    /**
     * @ORM\OneToMany(targetEntity=NotificationTemplateText::class, mappedBy="channelType", orphanRemoval=true)
     */
    private $notificationTemplateTexts;

    /**
     * ChannelType constructor.
     */
    public function __construct()
    {
        $this->notification = new ArrayCollection();
        $this->smsNotification = new ArrayCollection();
        $this->emailNotification = new ArrayCollection();
        $this->webNotification = new ArrayCollection();
        $this->notificationTemplateTexts = new ArrayCollection();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
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
            $notification->setChannelType($this);
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
            if ($notification->getChannelType() === $this) {
                $notification->setChannelType(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|SMSNotification[]
     */
    public function getSmsNotification(): Collection
    {
        return $this->smsNotification;
    }

    /**
     * @param SMSNotification $smsNotification
     * @return $this
     */
    public function addSmsNotification(SMSNotification $smsNotification): self
    {
        if (!$this->smsNotification->contains($smsNotification)) {
            $this->smsNotification[] = $smsNotification;
            $smsNotification->setChannelType($this);
        }
        return $this;
    }

    /**
     * @param SMSNotification $smsNotification
     * @return $this
     */
    public function removeSmsNotification(SMSNotification $smsNotification): self
    {
        if ($this->smsNotification->removeElement($smsNotification)) {
            // set the owning side to null (unless already changed)
            if ($smsNotification->getChannelType() === $this) {
                $smsNotification->setChannelType(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|EmailNotification[]
     */
    public function getEmailNotification(): Collection
    {
        return $this->emailNotification;
    }

    /**
     * @param EmailNotification $emailNotification
     * @return $this
     */
    public function addEmailNotification(EmailNotification $emailNotification): self
    {
        if (!$this->emailNotification->contains($emailNotification)) {
            $this->emailNotification[] = $emailNotification;
            $emailNotification->setChannelType($this);
        }
        return $this;
    }

    /**
     * @param EmailNotification $emailNotification
     * @return $this
     */
    public function removeEmailNotification(EmailNotification $emailNotification): self
    {
        if ($this->emailNotification->removeElement($emailNotification)) {
            // set the owning side to null (unless already changed)
            if ($emailNotification->getChannelType() === $this) {
                $emailNotification->setChannelType(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|WebNotification[]
     */
    public function getWebNotification(): Collection
    {
        return $this->webNotification;
    }

    /**
     * @param WebNotification $webNotification
     * @return $this
     */
    public function addWebNotification(WebNotification $webNotification): self
    {
        if (!$this->webNotification->contains($webNotification)) {
            $this->webNotification[] = $webNotification;
            $webNotification->setChannelType($this);
        }
        return $this;
    }

    /**
     * @param WebNotification $webNotification
     * @return $this
     */
    public function removeWebNotification(WebNotification $webNotification): self
    {
        if ($this->webNotification->removeElement($webNotification)) {
            // set the owning side to null (unless already changed)
            if ($webNotification->getChannelType() === $this) {
                $webNotification->setChannelType(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|NotificationTemplateText[]
     */
    public function getNotificationTemplateTexts(): Collection
    {
        return $this->notificationTemplateTexts;
    }

    /**
     * @param NotificationTemplateText $notificationTemplateText
     * @return $this
     */
    public function addNotificationTemplateText(NotificationTemplateText $notificationTemplateText): self
    {
        if (!$this->notificationTemplateTexts->contains($notificationTemplateText)) {
            $this->notificationTemplateTexts[] = $notificationTemplateText;
            $notificationTemplateText->setChannelType($this);
        }
        return $this;
    }

    /**
     * @param NotificationTemplateText $notificationTemplateText
     * @return $this
     */
    public function removeNotificationTemplateText(NotificationTemplateText $notificationTemplateText): self
    {
        if ($this->notificationTemplateTexts->removeElement($notificationTemplateText)) {
            // set the owning side to null (unless already changed)
            if ($notificationTemplateText->getChannelType() === $this) {
                $notificationTemplateText->setChannelType(null);
            }
        }
        return $this;
    }
}
