<?php

namespace App\Entity;

use App\Repository\MedicalHistoryRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MedicalHistoryRepository::class)
 * @ORM\Table(options={"comment":"История болезни"});
 */
class MedicalHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ истории болезни"}, nullable=false)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="medicalHistories")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $patient;

    /**
     * @ORM\Column(type="date", options={"comment"="Дата открытия"}, nullable=false)
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата закрытия"})
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Анамнез болезни"})
     */
    private $diseaseHistory;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TextByTemplate", inversedBy="lifeHistory")
     * @ORM\JoinColumn(name="life_history_id", nullable=true, onDelete="SET NULL")
     */
    private $lifeHistory;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true}, nullable=false)
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
     * @ORM\OneToOne(targetEntity=PatientDischargeEpicrisis::class, mappedBy="medicalHistory",cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $patientDischargeEpicrisis;

    /**
     * @ORM\OneToMany(targetEntity=PatientNotification::class, mappedBy="medicalHistory", orphanRemoval=true,cascade={"persist"})
     */
    private $patientNotifications;

    /**
     * @ORM\ManyToOne(targetEntity=ClinicalDiagnosis::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $clinicalDiagnosis;
    /**
     * MedicalHistory constructor.
     */
    public function __construct()
    {
        $this->medicalRecords = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->patientTestings = new ArrayCollection();
        $this->patientAppointments = new ArrayCollection();
        $this->patientNotifications = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Patient
     */
    public function getPatient(): Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient $patient
     *
     * @return $this
     */
    public function setPatient(Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateBegin(): DateTimeInterface
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
     * @return bool
     */
    public function getEnabled(): bool
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
     * @return Collection|MedicalRecord[]|null
     */
    public function getMedicalRecords(): ?Collection
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
     * @return Collection|Prescription[]|null
     */
    public function getPrescriptions(): ?Collection
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
     * @return Collection|PatientTesting[]|null
     */
    public function getPatientTestings(): ?Collection
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
     * @return Collection|PatientAppointment[]|null
     */
    public function getPatientAppointments(): ?Collection
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
     * @return string|null
     */
    public function getDiseaseHistory(): ?string
    {
        return $this->diseaseHistory;
    }

    /**
     * @param string|null $diseaseHistory
     *
     * @return $this
     */
    public function setDiseaseHistory(?string $diseaseHistory): self
    {
        $this->diseaseHistory = $diseaseHistory;
        return $this;
    }

    /**
     * @return PatientDischargeEpicrisis|null
     */
    public function getPatientDischargeEpicrisis(): ?PatientDischargeEpicrisis
    {
        return $this->patientDischargeEpicrisis;
    }

    /**
     * @param PatientDischargeEpicrisis|null $patientDischargeEpicrisis
     *
     * @return $this
     */
    public function setPatientDischargeEpicrisis(?PatientDischargeEpicrisis $patientDischargeEpicrisis): self
    {
        $this->patientDischargeEpicrisis = $patientDischargeEpicrisis;
        // set the owning side of the relation if necessary
        if ($patientDischargeEpicrisis->getMedicalHistory() !== $this) {
            $patientDischargeEpicrisis->setMedicalHistory($this);
        }
        return $this;
    }

    /**
     * @return TextByTemplate|null
     */
    public function getLifeHistory(): ?TextByTemplate
    {
        return $this->lifeHistory;
    }

    /**
     * @param TextByTemplate|null $lifeHistory
     * @return $this
     */
    public function setLifeHistory(?TextByTemplate $lifeHistory): self
    {
        $this->lifeHistory = $lifeHistory;
        return $this;
    }

    /**
     * @return Collection|PatientNotification[]|null
     */
    public function getPatientNotifications(): ?Collection
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
            $patientNotification->setMedicalHistory($this);
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
            if ($patientNotification->getMedicalHistory() === $this) {
                $patientNotification->setMedicalHistory(null);
            }
        }
        return $this;
    }

    /**
     * @return ClinicalDiagnosis
     */
    public function getClinicalDiagnosis(): ClinicalDiagnosis
    {
        return $this->clinicalDiagnosis;
    }

    /**
     * @param ClinicalDiagnosis $clinicalDiagnosis
     * @return $this
     */
    public function setClinicalDiagnosis(ClinicalDiagnosis $clinicalDiagnosis): self
    {
        $this->clinicalDiagnosis = $clinicalDiagnosis;
        return $this;
    }
}
