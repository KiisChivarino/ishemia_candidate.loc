<?php

namespace App\Entity;

use App\Repository\NotificationConfirmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Подтверждение уведомления
 * @ORM\Entity(repositoryClass=NotificationConfirmRepository::class)
 * @ORM\Table(options={"comment":"Подтверждение уведомления"});
 */
class NotificationConfirm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ подтверждения уведомления"})
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Статус подтверждения уведомления пациентом"})
     */
    private $isConfirmed;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Генерируемый код для подтверждения уведомления по ссылке"})
     */
    private $emailCode;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Генерируемый код для подтверждения уведомления по sms"})
     */
    private $smsCode;

    /**
     * @ORM\OneToMany(targetEntity=PatientNotification::class, mappedBy="notificationConfirm")
     */
    private $patientNotification;

    /**
     * NotificationConfirm constructor.
     */
    public function __construct()
    {
        $this->isConfirmed = false;
        $this->patientNotification = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     * @return $this
     */
    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmailCode(): ?string
    {
        return $this->emailCode;
    }

    /**
     * @param string $emailCode
     * @return $this
     */
    public function setEmailCode(string $emailCode): self
    {
        $this->emailCode = $emailCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSmsCode(): ?string
    {
        return $this->smsCode;
    }

    /**
     * @param string $smsCode
     * @return $this
     */
    public function setSmsCode(string $smsCode): self
    {
        $this->smsCode = $smsCode;

        return $this;
    }

    /**
     * @return Collection|PatientNotification[]
     */
    public function getPatientNotification(): Collection
    {
        return $this->patientNotification;
    }

    /**
     * @param PatientNotification $patientNotification
     * @return $this
     */
    public function addPatientNotification(PatientNotification $patientNotification): self
    {
        if (!$this->patientNotification->contains($patientNotification)) {
            $this->patientNotification[] = $patientNotification;
            $patientNotification->setNotificationConfirm($this);
        }
        return $this;
    }

    /**
     * @param PatientNotification $patientNotification
     * @return $this
     */
    public function removePatientNotification(PatientNotification $patientNotification): self
    {
        if ($this->patientNotification->removeElement($patientNotification)) {
            // set the owning side to null (unless already changed)
            if ($patientNotification->getNotificationConfirm() === $this) {
                $patientNotification->setNotificationConfirm(null);
            }
        }
        return $this;
    }
}
