<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\Table(options={"comment":"Уведомление"});
 *
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
     * @ORM\ManyToOne(targetEntity=NotificationType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $notificationType;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalRecord;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время создания уведомления"})
     */
    private $notificationTime;

    /**
     * @ORM\Column(type="text", options={"comment"="Текст уведомления"})
     */
    private $text;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalHistory;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return NotificationType|null
     */
    public function getNotificationType(): ?NotificationType
    {
        return $this->notificationType;
    }

    /**
     * @param NotificationType|null $notificationType
     *
     * @return $this
     */
    public function setNotificationType(?NotificationType $notificationType): self
    {
        $this->notificationType = $notificationType;
        return $this;
    }

    /**
     * @return MedicalRecord|null
     */
    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
    }

    /**
     * @param MedicalRecord|null $medicalRecord
     *
     * @return $this
     */
    public function setMedicalRecord(?MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * @return Staff|null
     */
    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    /**
     * @param Staff|null $staff
     *
     * @return $this
     */
    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

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
     *
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
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return MedicalHistory|null
     */
    public function getMedicalHistory(): ?MedicalHistory
    {
        return $this->medicalHistory;
    }

    /**
     * @param MedicalHistory|null $medicalHistory
     *
     * @return $this
     */
    public function setMedicalHistory(?MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }
}
