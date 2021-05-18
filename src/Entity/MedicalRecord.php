<?php

namespace App\Entity;

use App\Repository\MedicalRecordRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MedicalRecord
 * @ORM\Entity(repositoryClass=MedicalRecordRepository::class)
 * @ORM\Table(options={"comment":"Запись в историю болезни"});
 *
 * @package App\Entity
 */
class MedicalRecord
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ записи в историю болезни"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="medicalRecords")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $medicalHistory;

    /**
     * @ORM\Column(type="date", options={"comment"="Дата создания записи"})
     */
    private $recordDate;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Комментарий"})
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=PatientTesting::class, mappedBy="medicalRecord")
     */
    private $patientTestings;

    /**
     * @ORM\OneToMany(targetEntity=PatientAppointment::class, mappedBy="medicalRecord", cascade={"persist"})
     */
    private $patientAppointments;

    /**
     * @ORM\OneToMany(targetEntity=Prescription::class, mappedBy="medicalRecord")
     */
    private $prescriptions;

    /**
     * @ORM\OneToMany(targetEntity=PatientNotification::class, mappedBy="medicalRecord", orphanRemoval=true, cascade={"persist"})
     */
    private $patientNotifications;

    /**
     * MedicalRecord constructor.
     */
    public function __construct()
    {
        $this->patientTestings = new ArrayCollection();
        $this->patientAppointments = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->patientNotifications = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return DateTimeInterface|null
     */
    public function getRecordDate(): ?DateTimeInterface
    {
        return $this->recordDate;
    }

    /**
     * @param DateTimeInterface $recordDate
     *
     * @return $this
     */
    public function setRecordDate(DateTimeInterface $recordDate): self
    {
        $this->recordDate = $recordDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
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
     * @return Collection|PatientTesting[]
     */
    public function getPatientTestings(): Collection
    {
        return $this->patientTestings;
    }

    /**
     * @param PatientTesting $patientTesting
     *
     * @return $this
     */
    public function addPatientTesting(PatientTesting $patientTesting): self
    {
        if (!$this->patientTestings->contains($patientTesting)) {
            $this->patientTestings[] = $patientTesting;
            $patientTesting->setMedicalRecord($this);
        }
        return $this;
    }

    /**
     * @param PatientTesting $patientTesting
     *
     * @return $this
     */
    public function removePatientTesting(PatientTesting $patientTesting): self
    {
        if ($this->patientTestings->contains($patientTesting)) {
            $this->patientTestings->removeElement($patientTesting);
            // set the owning side to null (unless already changed)
            if ($patientTesting->getMedicalRecord() === $this) {
                $patientTesting->setMedicalRecord(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|PatientAppointment[]
     */
    public function getPatientAppointments(): Collection
    {
        return $this->patientAppointments;
    }

    /**
     * @param PatientAppointment $patientAppointment
     *
     * @return $this
     */
    public function addPatientAppointment(PatientAppointment $patientAppointment): self
    {
        if (!$this->patientAppointments->contains($patientAppointment)) {
            $this->patientAppointments[] = $patientAppointment;
            $patientAppointment->setMedicalRecord($this);
        }
        return $this;
    }

    /**
     * @param PatientAppointment $patientAppointment
     *
     * @return $this
     */
    public function removePatientAppointment(PatientAppointment $patientAppointment): self
    {
        if ($this->patientAppointments->contains($patientAppointment)) {
            $this->patientAppointments->removeElement($patientAppointment);
            // set the owning side to null (unless already changed)
            if ($patientAppointment->getMedicalRecord() === $this) {
                $patientAppointment->setMedicalRecord(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Prescription[]
     */
    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }

    /**
     * @param Prescription $prescription
     * @return $this
     */
    public function addPrescription(Prescription $prescription): self
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions[] = $prescription;
            $prescription->setMedicalRecord($this);
        }

        return $this;
    }

    /**
     * @param Prescription $prescription
     * @return $this
     */
    public function removePrescription(Prescription $prescription): self
    {
        if ($this->prescriptions->contains($prescription)) {
            $this->prescriptions->removeElement($prescription);
            // set the owning side to null (unless already changed)
            if ($prescription->getMedicalRecord() === $this) {
                $prescription->setMedicalRecord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PatientNotification[]
     */
    public function getPatientNotifications(): Collection
    {
        return $this->patientNotifications;
    }

    /**
     * @param PatientNotification $patientNotification
     * @return $this
     */
    public function addPatientNotification(PatientNotification $patientNotification): self
    {
        if (!$this->patientNotifications->contains($patientNotification)) {
            $this->patientNotifications[] = $patientNotification;
            $patientNotification->setMedicalRecord($this);
        }

        return $this;
    }

    /**
     * @param PatientNotification $patientNotification
     * @return $this
     */
    public function removePatientNotification(PatientNotification $patientNotification): self
    {
        if ($this->patientNotifications->removeElement($patientNotification)) {
            // set the owning side to null (unless already changed)
            if ($patientNotification->getMedicalRecord() === $this) {
                $patientNotification->setMedicalRecord(null);
            }
        }

        return $this;
    }
}
