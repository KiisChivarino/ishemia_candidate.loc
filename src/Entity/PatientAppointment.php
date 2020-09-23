<?php

namespace App\Entity;

use App\Repository\PatientAppointmentRepository;
use DateTimeInterface;
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
     * @ORM\Column(type="integer", options={"comment"="Ключ приема пациента"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="patientAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalRecord;

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="patientAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Комментарий врача"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=AppointmentType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appointmentType;

    /**
     * @ORM\Column(type="datetime", options={"comment"="Дата и время приема"})
     */
    private $appointmentTime;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="patientAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalHistory;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Подтверждение пользователем", "default"=false})
     */
    private $isConfirmed;

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @param DateTimeInterface $appointmentTime
     *
     * @return $this
     */
    public function setAppointmentTime(DateTimeInterface $appointmentTime): self
    {
        $this->appointmentTime = $appointmentTime;
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

    /**
     * @return bool|null
     */
    public function getIsConfirmed(): ?bool
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
}
