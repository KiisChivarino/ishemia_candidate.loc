<?php

namespace App\Entity;

use App\Repository\MedicalHistoryRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass=MedicalHistoryRepository::class)
 * @ORM\Table(options={"comment":"История болезни"});
 */
class MedicalHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ истории болезни"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="medicalHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="date", options={"comment"="Дата открытия"})
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата закрытия"})
     */
    private $dateEnd;

    /**
     * @ORM\ManyToOne(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mainDisease;

    /**
     * Фоновые заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @JoinTable(name="background_diseases")
     */
    private $backgroundDiseases;

    /**
     * Осложнения основного заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @JoinTable(name="complications")
     */
    private $complications;

    /**
     * Сопутствующие заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @JoinTable(name="concomitant_diseases")
     */
    private $concomitantDiseases;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Анамнез болезни"})
     */
    private $diseaseHistory;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Анамнез жизни"})
     */
    private $lifeHistory;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=MedicalRecord::class, mappedBy="medicalHistory", orphanRemoval=true)
     */
    private $medicalRecords;

    /**
     * @ORM\OneToMany(targetEntity=Prescription::class, mappedBy="medicalHistory", orphanRemoval=true)
     */
    private $prescriptions;

    /**
     * @ORM\OneToMany(targetEntity=PatientTesting::class, mappedBy="medicalHistory", orphanRemoval=true)
     */
    private $patientTestings;

    /**
     * @ORM\OneToMany(targetEntity=PatientAppointment::class, mappedBy="medicalHistory", orphanRemoval=true,cascade={"persist"})
     */
    private $patientAppointments;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="medicalHistory", orphanRemoval=true)
     */
    private $notifications;

    /**
     * MedicalHistory constructor.
     */
    public function __construct()
    {
        $this->medicalRecords = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->patientTestings = new ArrayCollection();
        $this->patientAppointments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->backgroundDiseases = new ArrayCollection();
        $this->complications = new ArrayCollection();
        $this->concomitantDiseases = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     *
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateBegin(): ?DateTimeInterface
    {
        return $this->dateBegin;
    }

    /**
     * @param DateTimeInterface $dateBegin
     *
     * @return $this
     */
    public function setDateBegin(DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    /**
     * @param DateTimeInterface|null $dateEnd
     *
     * @return $this
     */
    public function setDateEnd(?DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;
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
     * @return Collection|MedicalRecord[]
     */
    public function getMedicalRecords(): Collection
    {
        return $this->medicalRecords;
    }

    /**
     * @param MedicalRecord $medicalRecord
     *
     * @return $this
     */
    public function addMedicalRecord(MedicalRecord $medicalRecord): self
    {
        if (!$this->medicalRecords->contains($medicalRecord)) {
            $this->medicalRecords[] = $medicalRecord;
            $medicalRecord->setMedicalHistory($this);
        }
        return $this;
    }

    /**
     * @param MedicalRecord $medicalRecord
     *
     * @return $this
     */
    public function removeMedicalRecord(MedicalRecord $medicalRecord): self
    {
        if ($this->medicalRecords->contains($medicalRecord)) {
            $this->medicalRecords->removeElement($medicalRecord);
            // set the owning side to null (unless already changed)
            if ($medicalRecord->getMedicalHistory() === $this) {
                $medicalRecord->setMedicalHistory(null);
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
     *
     * @return $this
     */
    public function addPrescription(Prescription $prescription): self
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions[] = $prescription;
            $prescription->setMedicalHistory($this);
        }
        return $this;
    }

    /**
     * @param Prescription $prescription
     *
     * @return $this
     */
    public function removePrescription(Prescription $prescription): self
    {
        if ($this->prescriptions->contains($prescription)) {
            $this->prescriptions->removeElement($prescription);
            // set the owning side to null (unless already changed)
            if ($prescription->getMedicalHistory() === $this) {
                $prescription->setMedicalHistory(null);
            }
        }
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
            $patientTesting->setMedicalHistory($this);
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
            if ($patientTesting->getMedicalHistory() === $this) {
                $patientTesting->setMedicalHistory(null);
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
            $patientAppointment->setMedicalHistory($this);
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
            if ($patientAppointment->getMedicalHistory() === $this) {
                $patientAppointment->setMedicalHistory(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * @param Notification $notification
     *
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setMedicalHistory($this);
        }
        return $this;
    }

    /**
     * @param Notification $notification
     *
     * @return $this
     */
    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getMedicalHistory() === $this) {
                $notification->setMedicalHistory(null);
            }
        }
        return $this;
    }

    /**
     * @return Diagnosis|null
     */
    public function getMainDisease(): ?Diagnosis
    {
        return $this->mainDisease;
    }

    /**
     * @param Diagnosis|null $MainDisease
     *
     * @return $this
     */
    public function setMainDisease(?Diagnosis $MainDisease): self
    {
        $this->mainDisease = $MainDisease;
        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getBackgroundDiseases(): Collection
    {
        return $this->backgroundDiseases;
    }

    public function addBackgroundDisease(Diagnosis $backgroundDisease): self
    {
        if (!$this->backgroundDiseases->contains($backgroundDisease)) {
            $this->backgroundDiseases[] = $backgroundDisease;
        }
        return $this;
    }

    public function removeBackgroundDisease(Diagnosis $backgroundDisease): self
    {
        if ($this->backgroundDiseases->contains($backgroundDisease)) {
            $this->backgroundDiseases->removeElement($backgroundDisease);
        }
        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getComplications(): Collection
    {
        return $this->complications;
    }

    public function addComplication(Diagnosis $complication): self
    {
        if (!$this->complications->contains($complication)) {
            $this->complications[] = $complication;
        }
        return $this;
    }

    public function removeComplication(Diagnosis $complication): self
    {
        if ($this->complications->contains($complication)) {
            $this->complications->removeElement($complication);
        }
        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getConcomitantDiseases(): Collection
    {
        return $this->concomitantDiseases;
    }

    public function addConcomitantDisease(Diagnosis $concomitantDisease): self
    {
        if (!$this->concomitantDiseases->contains($concomitantDisease)) {
            $this->concomitantDiseases[] = $concomitantDisease;
        }
        return $this;
    }

    public function removeConcomitantDisease(Diagnosis $concomitantDisease): self
    {
        if ($this->concomitantDiseases->contains($concomitantDisease)) {
            $this->concomitantDiseases->removeElement($concomitantDisease);
        }
        return $this;
    }

    public function getDiseaseHistory(): ?string
    {
        return $this->diseaseHistory;
    }

    public function setDiseaseHistory(?string $diseaseHistory): self
    {
        $this->diseaseHistory = $diseaseHistory;
        return $this;
    }

    public function getLifeHistory(): ?string
    {
        return $this->lifeHistory;
    }

    public function setLifeHistory(?string $lifeHistory): self
    {
        $this->lifeHistory = $lifeHistory;
        return $this;
    }
}
