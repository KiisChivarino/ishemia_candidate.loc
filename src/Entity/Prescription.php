<?php

namespace App\Entity;

use App\Repository\PrescriptionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrescriptionRepository::class)
 * @ORM\Table(options={"comment":"Назначение"});
 */
class Prescription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ назначения"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="prescriptions")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $medicalHistory;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Назначено", "default"=false})
     */
    private $isCompleted;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Подтверждение назначения пациентом", "default"=false})
     */
    private $isPatientConfirmed;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="prescriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Описание назначения"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=PrescriptionMedicine::class, mappedBy="prescription", orphanRemoval=true)
     */
    private $prescriptionMedicines;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время создания назначения"})
     */
    private $createdTime;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment"="Дата и время факта назначения"})
     */
    private $completedTime;

    /**
     * @ORM\OneToMany(targetEntity=PrescriptionTesting::class, mappedBy="prescription", orphanRemoval=true)
     */
    private $prescriptionTestings;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="prescriptions")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $medicalRecord;

    /**
     * @ORM\OneToMany(targetEntity=PrescriptionAppointment::class, mappedBy="prescription", orphanRemoval=true)
     */
    private $prescriptionAppointments;

    /**
     * Prescription constructor.
     */
    public function __construct()
    {
        $this->prescriptionMedicines = new ArrayCollection();
        $this->prescriptionTestings = new ArrayCollection();
        $this->prescriptionAppointments = new ArrayCollection();
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
     * @return bool|null
     */
    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPatientConfirmed(): ?bool
    {
        return $this->isPatientConfirmed;
    }

    /**
     * @param bool $isPatientConfirmed
     *
     * @return $this
     */
    public function setIsPatientConfirmed(bool $isPatientConfirmed): self
    {
        $this->isPatientConfirmed = $isPatientConfirmed;
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection|PrescriptionMedicine[]
     */
    public function getPrescriptionMedicines(): Collection
    {
        return $this->prescriptionMedicines->filter(
            function (PrescriptionMedicine $prescriptionMedicine) {
                return $prescriptionMedicine->getEnabled();
            }
        );
    }

    /**
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return $this
     */
    public function addPrescriptionMedicine(PrescriptionMedicine $prescriptionMedicine): self
    {
        if (!$this->prescriptionMedicines->contains($prescriptionMedicine)) {
            $this->prescriptionMedicines[] = $prescriptionMedicine;
            $prescriptionMedicine->setPrescription($this);
        }
        return $this;
    }

    /**
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return $this
     */
    public function removePrescriptionMedicine(PrescriptionMedicine $prescriptionMedicine): self
    {
        if ($this->prescriptionMedicines->contains($prescriptionMedicine)) {
            $this->prescriptionMedicines->removeElement($prescriptionMedicine);
            // set the owning side to null (unless already changed)
            if ($prescriptionMedicine->getPrescription() === $this) {
                $prescriptionMedicine->setPrescription(null);
            }
        }
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
     * @return DateTimeInterface|null
     */
    public function getCreatedTime(): ?DateTimeInterface
    {
        return $this->createdTime;
    }

    /**
     * @param DateTimeInterface $createdTime
     *
     * @return $this
     */
    public function setCreatedTime(DateTimeInterface $createdTime): self
    {
        $this->createdTime = $createdTime;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCompletedTime(): ?DateTimeInterface
    {
        return $this->completedTime;
    }

    /**
     * @param DateTimeInterface|null $completedTime
     *
     * @return $this
     */
    public function setCompletedTime(?DateTimeInterface $completedTime): self
    {
        $this->completedTime = $completedTime;
        return $this;
    }

    /**
     * @return Collection|PrescriptionTesting[]
     */
    public function getPrescriptionTestings(): Collection
    {
        return $this->prescriptionTestings->filter(
            function (PrescriptionTesting $prescriptionTesting) {
                return $prescriptionTesting->getEnabled();
            }
        );
    }

    /**
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return $this
     */
    public function addPrescriptionTesting(PrescriptionTesting $prescriptionTesting): self
    {
        if (!$this->prescriptionTestings->contains($prescriptionTesting)) {
            $this->prescriptionTestings[] = $prescriptionTesting;
            $prescriptionTesting->setPrescription($this);
        }
        return $this;
    }

    /**
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return $this
     */
    public function removePrescriptionTesting(PrescriptionTesting $prescriptionTesting): self
    {
        if ($this->prescriptionTestings->contains($prescriptionTesting)) {
            $this->prescriptionTestings->removeElement($prescriptionTesting);
            // set the owning side to null (unless already changed)
            if ($prescriptionTesting->getPrescription() === $this) {
                $prescriptionTesting->setPrescription(null);
            }
        }
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
     * @return Collection|PrescriptionAppointment[]
     */
    public function getPrescriptionAppointments(): Collection
    {
        return $this->prescriptionAppointments->filter(
            function (PrescriptionAppointment $prescriptionMedicine) {
                return $prescriptionMedicine->getEnabled();
            }
        );
    }

    public function addPrescriptionAppointment(PrescriptionAppointment $prescriptionAppointment): self
    {
        if (!$this->prescriptionAppointments->contains($prescriptionAppointment)) {
            $this->prescriptionAppointments[] = $prescriptionAppointment;
            $prescriptionAppointment->setPrescription($this);
        }

        return $this;
    }

    public function removePrescriptionAppointment(PrescriptionAppointment $prescriptionAppointment): self
    {
        if ($this->prescriptionAppointments->removeElement($prescriptionAppointment)) {
            // set the owning side to null (unless already changed)
            if ($prescriptionAppointment->getPrescription() === $this) {
                $prescriptionAppointment->setPrescription(null);
            }
        }

        return $this;
    }
}
