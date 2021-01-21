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
     * @ORM\OneToOne(targetEntity=PrescriptionMedicine::class, mappedBy="notificationConfirm")
     */
    private $prescriptionMedicine;

    /**
     * @ORM\OneToOne(targetEntity=PrescriptionTesting::class, mappedBy="notificationConfirm")
     */
    private $prescriptionTesting;

    /**
     * @ORM\OneToOne(targetEntity=PrescriptionAppointment::class, mappedBy="notificationConfirm")
     */
    private $prescriptionAppointment;

    public function __construct()
    {
        $this->patientNotification = new ArrayCollection();
        $this->isConfirmed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function getEmailCode(): ?string
    {
        return $this->emailCode;
    }

    public function setEmailCode(string $emailCode): self
    {
        $this->emailCode = $emailCode;

        return $this;
    }

    public function getSmsCode(): ?string
    {
        return $this->smsCode;
    }

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

    public function addPatientNotification(PatientNotification $patientNotification): self
    {
        if (!$this->patientNotification->contains($patientNotification)) {
            $this->patientNotification[] = $patientNotification;
            $patientNotification->setNotificationConfirm($this);
        }

        return $this;
    }

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

    public function getPrescriptionMedicine(): ?PrescriptionMedicine
    {
        return $this->prescriptionMedicine;
    }

    public function setPrescriptionMedicine(?PrescriptionMedicine $prescriptionMedicine): self
    {
        // unset the owning side of the relation if necessary
        if ($prescriptionMedicine === null && $this->prescriptionMedicine !== null) {
            $this->prescriptionMedicine->setNotificationConfirm(null);
        }

        // set the owning side of the relation if necessary
        if ($prescriptionMedicine !== null && $prescriptionMedicine->getNotificationConfirm() !== $this) {
            $prescriptionMedicine->setNotificationConfirm($this);
        }

        $this->prescriptionMedicine = $prescriptionMedicine;

        return $this;
    }

    public function getPrescriptionTesting(): ?PrescriptionTesting
    {
        return $this->prescriptionTesting;
    }

    public function setPrescriptionTesting(?PrescriptionTesting $prescriptionTesting): self
    {
        // unset the owning side of the relation if necessary
        if ($prescriptionTesting === null && $this->prescriptionTesting !== null) {
            $this->prescriptionTesting->setNotificationConfirm(null);
        }

        // set the owning side of the relation if necessary
        if ($prescriptionTesting !== null && $prescriptionTesting->getNotificationConfirm() !== $this) {
            $prescriptionTesting->setNotificationConfirm($this);
        }

        $this->prescriptionTesting = $prescriptionTesting;

        return $this;
    }

    public function getPrescriptionAppointment(): ?PrescriptionAppointment
    {
        return $this->prescriptionAppointment;
    }

    public function setPrescriptionAppointment(?PrescriptionAppointment $prescriptionAppointment): self
    {
        // unset the owning side of the relation if necessary
        if ($prescriptionAppointment === null && $this->prescriptionAppointment !== null) {
            $this->prescriptionAppointment->setNotificationConfirm(null);
        }

        // set the owning side of the relation if necessary
        if ($prescriptionAppointment !== null && $prescriptionAppointment->getNotificationConfirm() !== $this) {
            $prescriptionAppointment->setNotificationConfirm($this);
        }

        $this->prescriptionAppointment = $prescriptionAppointment;

        return $this;
    }

}
