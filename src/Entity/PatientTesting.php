<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сдача анализов пациента
 * @ORM\Entity(repositoryClass="App\Repository\PatientTestingRepository")
 * @ORM\Table(options={"comment":"Сдача анализов (обследование) пациента"});
 */
class PatientTesting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ сдачи анализов"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnalysisGroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analysisGroup;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата проведенного тестирования"})
     */
    private $analysisDate;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Статус принятия в работу врачом", "default"=false})
     */
    private $processed;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientTestingResult", mappedBy="patientTesting", orphanRemoval=true)
     */
    private $patientTestingResults;

    /**
     * @ORM\OneToOne(targetEntity=PrescriptionTesting::class, mappedBy="patientTesting", cascade={"persist", "remove"})
     */
    private $prescriptionTesting;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalHistory::class, inversedBy="patientTestings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medicalHistory;

    /**
     * @ORM\ManyToOne(targetEntity=MedicalRecord::class, inversedBy="patientTestings")
     */
    private $medicalRecord;

    /**
     * @ORM\Column(type="date")
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEnd;

    /**
     * PatientTesting constructor.
     */
    public function __construct()
    {
        $this->patientTestingResults = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AnalysisGroup|null
     */
    public function getAnalysisGroup(): ?AnalysisGroup
    {
        return $this->analysisGroup;
    }

    /**
     * @param AnalysisGroup|null $analysisGroup
     *
     * @return $this
     */
    public function setAnalysisGroup(?AnalysisGroup $analysisGroup): self
    {
        $this->analysisGroup = $analysisGroup;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAnalysisDate(): ?DateTimeInterface
    {
        return $this->analysisDate;
    }

    /**
     * @param DateTimeInterface|null $analysisDate
     *
     * @return $this
     */
    public function setAnalysisDate(?DateTimeInterface $analysisDate): self
    {
        $this->analysisDate = $analysisDate;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getProcessed(): ?bool
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     *
     * @return $this
     */
    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;
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
     * @return Collection|PatientTestingResult[]
     */
    public function getPatientTestingResults(): Collection
    {
        return $this->patientTestingResults;
    }

    /**
     * @param PatientTestingResult $patientTestingResult
     *
     * @return $this
     */
    public function addPatientTestingResult(PatientTestingResult $patientTestingResult): self
    {
        if (!$this->patientTestingResults->contains($patientTestingResult)) {
            $this->patientTestingResults[] = $patientTestingResult;
            $patientTestingResult->setPatientTesting($this);
        }
        return $this;
    }

    /**
     * @param PatientTestingResult $patientTestingResult
     *
     * @return $this
     */
    public function removePatientTestingResult(PatientTestingResult $patientTestingResult): self
    {
        if ($this->patientTestingResults->contains($patientTestingResult)) {
            $this->patientTestingResults->removeElement($patientTestingResult);
            // set the owning side to null (unless already changed)
            if ($patientTestingResult->getPatientTesting() === $this) {
                $patientTestingResult->setPatientTesting(null);
            }
        }
        return $this;
    }

    /**
     * @return PrescriptionTesting|null
     */
    public function getPrescriptionTesting(): ?PrescriptionTesting
    {
        return $this->prescriptionTesting;
    }

    /**
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return $this
     */
    public function setPrescriptionTesting(PrescriptionTesting $prescriptionTesting): self
    {
        $this->prescriptionTesting = $prescriptionTesting;

        // set the owning side of the relation if necessary
        if ($prescriptionTesting->getPatientTesting() !== $this) {
            $prescriptionTesting->setPatientTesting($this);
        }
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

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }
}