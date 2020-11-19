<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Персонал
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 * @ORM\Table(options={"comment":"Персонал"});
 */
class Staff
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ персонала"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital", inversedBy="staff")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hospital;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(nullable=false)
     */
    private $position;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AuthUser", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $AuthUser;

    /**
     * @ORM\OneToMany(targetEntity=Prescription::class, mappedBy="staff")
     */
    private $prescriptions;

    /**
     * @ORM\OneToMany(targetEntity=PrescriptionTesting::class, mappedBy="staff")
     */
    private $prescriptionTestings;

    /**
     * @ORM\OneToMany(targetEntity=PatientAppointment::class, mappedBy="staff")
     */
    private $patientAppointments;

    /**
     * @ORM\OneToMany(targetEntity=PrescriptionMedicine::class, mappedBy="staff")
     */
    private $prescriptionMedicines;

    /**
     * Staff constructor.
     */
    public function __construct()
    {
        $this->prescriptions = new ArrayCollection();
        $this->prescriptionTestings = new ArrayCollection();
        $this->prescriptionMedicines = new ArrayCollection();
        $this->patientAppointments = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Hospital|null
     */
    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    /**
     * @param Hospital|null $hospital
     *
     * @return $this
     */
    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }

    /**
     * @return Position|null
     */
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * @param Position|null $position
     *
     * @return $this
     */
    public function setPosition(?Position $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return AuthUser|null
     */
    public function getAuthUser(): ?AuthUser
    {
        return $this->AuthUser;
    }

    /**
     * @param AuthUser $AuthUser
     *
     * @return $this
     */
    public function setAuthUser(AuthUser $AuthUser): self
    {
        $this->AuthUser = $AuthUser;
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
            $prescription->setStaff($this);
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
            if ($prescription->getStaff() === $this) {
                $prescription->setStaff(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|PrescriptionTesting[]
     */
    public function getPrescriptionTestings(): Collection
    {
        return $this->prescriptionTestings;
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
            $prescriptionTesting->setStaff($this);
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
            if ($prescriptionTesting->getStaff() === $this) {
                $prescriptionTesting->setStaff(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|PrescriptionMedicine[]
     */
    public function getPrescriptionMedicines(): Collection
    {
        return $this->prescriptionMedicines;
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
            $prescriptionMedicine->setStaff($this);
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
            if ($prescriptionMedicine->getStaff() === $this) {
                $prescriptionMedicine->setStaff(null);
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
            $patientAppointment->setStaff($this);
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
            if ($patientAppointment->getStaff() === $this) {
                $patientAppointment->setStaff(null);
            }
        }
        return $this;
    }
}
