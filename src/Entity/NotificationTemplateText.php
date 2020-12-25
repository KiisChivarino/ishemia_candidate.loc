<?php

namespace App\Entity;

use App\Repository\NotificationTemplateTextRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification Template Text
 * @ORM\Entity(repositoryClass=NotificationTemplateTextRepository::class)
 * @ORM\Table(options={"comment":"Текст шаблона уведомления"});
 */
class NotificationTemplateText
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ текста шаблона уведомления"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=NotificationTemplate::class, inversedBy="notificationTemplateTexts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notificationTemplate;

    /**
     * @ORM\ManyToOne(targetEntity=ChannelType::class, inversedBy="notificationTemplateTexts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelType;

    /**
     * @ORM\Column(type="text", options={"comment"="Текст уведомления"})
     */
    private $text;

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
     * @return NotificationTemplate|null
     */
    public function getNotificationTemplate(): ?NotificationTemplate
    {
        return $this->notificationTemplate;
    }

    /**
     * @param NotificationTemplate|null $notificationTemplate
     * @return $this
     */
    public function setNotificationTemplate(?NotificationTemplate $notificationTemplate): self
    {
        $this->notificationTemplate = $notificationTemplate;

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
