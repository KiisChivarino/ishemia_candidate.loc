<?php

namespace App\Entity;

use App\Repository\PatientAppointmentRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PatientAppointment
 * @ORM\Entity(repositoryClass=PatientAppointmentRepository::class)
 * @ORM\Table(options={"comment":"Прием пациента"});
 *
 * @package App\Entity
 */
class PatientAppointment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ приема пациента"}, nullable=false)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="patientAppointments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $medicalRecord;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="patientAppointments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalHistory;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="patientAppointments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $staff;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Рекомендации врача"})
     */
    private $recommendation;

    /**
     * @ORM\ManyToOne(targetEntity=AppointmentType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appointmentType;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment"="Фактические дата и время приема"})
     */
    private $appointmentTime;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true}, nullable=false)
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Подтверждение пользователем", "default"=false}, nullable=false)
     */
    private $isConfirmed;

    /**
     * @ORM\ManyToMany(targetEntity=Complaint::class)
     */
    private $complaints;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Комментарий врача по жалобам"})
     */
    private $complaintsComment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TextByTemplate", inversedBy="patientAppointment")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $objectiveStatus;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Терапия"})
     */
    private $therapy;

    /**
     * @ORM\OneToOne(targetEntity=PrescriptionAppointment::class, mappedBy="patientAppointment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $prescriptionAppointment;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг первого приема при заведении истории болезни"}, nullable=false)
     */
    private $isFirst;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг: прием по плану", "default"=false}, nullable=false)
     */
    private $isByPlan;

    /**
     * @ORM\ManyToOne(targetEntity=PlanAppointment::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $planAppointment;

    /**
     * PatientAppointment constructor.
     */
    public function __construct()
    {
        $this->complaints = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return string|null
     */
    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    /**
     * @param string|null $recommendation
     *
     * @return $this
     */
    public function setRecommendation(?string $recommendation): self
    {
        $this->recommendation = $recommendation;
        return $this;
    }

    /**
     * @return AppointmentType|null
     */
    public function getAppointmentType(): ?AppointmentType
    {
        return $this->appointmentType;
    }

    /**
     * @param AppointmentType|null $appointmentType
     *
     * @return $this
     */
    public function setAppointmentType(?AppointmentType $appointmentType): self
    {
        $this->appointmentType = $appointmentType;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAppointmentTime(): ?DateTimeInterface
    {
        return $this->appointmentTime;
    }

    /**
     * @param DateTimeInterface|null $appointmentTime
     *
     * @return $this
     */
    public function setAppointmentTime(?DateTimeInterface $appointmentTime): self
    {
        $this->appointmentTime = $appointmentTime;
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
     * @return MedicalHistory
     */
    public function getMedicalHistory(): MedicalHistory
    {
        return $this->medicalHistory;
    }

    /**
     * @param MedicalHistory $medicalHistory
     *
     * @return $this
     */
    public function setMedicalHistory(MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     *
     * @return $this
     */
    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    /**
     * @return Collection|Complaint[]
     */
    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    /**
     * @param Complaint $complaint
     *
     * @return $this
     */
    public function addComplaint(Complaint $complaint): self
    {
        if (!$this->complaints->contains($complaint)) {
            $this->complaints[] = $complaint;
        }
        return $this;
    }

    /**
     * @param Complaint $complaint
     *
     * @return $this
     */
    public function removeComplaint(Complaint $complaint): self
    {
        if ($this->complaints->contains($complaint)) {
            $this->complaints->removeElement($complaint);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComplaintsComment(): ?string
    {
        return $this->complaintsComment;
    }

    /**
     * @param string|null $complaintsComment
     *
     * @return $this
     */
    public function setComplaintsComment(?string $complaintsComment): self
    {
        $this->complaintsComment = $complaintsComment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTherapy(): ?string
    {
        return $this->therapy;
    }

    /**
     * @param string|null $therapy
     *
     * @return $this
     */
    public function setTherapy(?string $therapy): self
    {
        $this->therapy = $therapy;
        return $this;
    }

    /**
     * @return TextByTemplate|null
     */
    public function getObjectiveStatus(): ?TextByTemplate
    {
        return $this->objectiveStatus;
    }

    /**
     * @param TextByTemplate|null $objectiveStatus
     * @return $this
     */
    public function setObjectiveStatus(?TextByTemplate $objectiveStatus): self
    {
        $this->objectiveStatus = $objectiveStatus;
        return $this;
    }

    /**
     * @return PrescriptionAppointment|null
     */
    public function getPrescriptionAppointment(): ?PrescriptionAppointment
    {
        return $this->prescriptionAppointment;
    }

    /**
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return $this
     */
    public function setPrescriptionAppointment(PrescriptionAppointment $prescriptionAppointment): self
    {
        $this->prescriptionAppointment = $prescriptionAppointment;

        // set the owning side of the relation if necessary
        if ($prescriptionAppointment->getPatientAppointment() !== $this) {
            $prescriptionAppointment->setPatientAppointment($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsFirst(): bool
    {
        return $this->isFirst;
    }

    /**
     * @param bool $isFirst
     * @return $this
     */
    public function setIsFirst(bool $isFirst): self
    {
        $this->isFirst = $isFirst;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsByPlan(): bool
    {
        return $this->isByPlan;
    }

    /**
     * @param bool $isByPlan
     * @return $this
     */
    public function setIsByPlan(bool $isByPlan): self
    {
        $this->isByPlan = $isByPlan;
        return $this;
    }

    /**
     * @return PlanAppointment|null
     */
    public function getPlanAppointment(): ?PlanAppointment
    {
        return $this->planAppointment;
    }

    /**
     * @param PlanAppointment|null $planAppointment
     * @return $this
     */
    public function setPlanAppointment(?PlanAppointment $planAppointment): self
    {
        $this->planAppointment = $planAppointment;
        return $this;
    }
}
