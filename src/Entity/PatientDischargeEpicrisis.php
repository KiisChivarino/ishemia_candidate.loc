<?php

namespace App\Entity;

use App\Repository\PatientDischargeEpicrisisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientDischargeEpicrisisRepository::class)
 * @ORM\Table(options={"comment":"Выписные эпикризы"});
 */
class PatientDischargeEpicrisis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ выписного эпикриза"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=MedicalHistory::class, inversedBy="patientDischargeEpicrisis", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $medicalHistory;

    /**
     * @ORM\OneToMany(targetEntity=DischargeEpicrisisFile::class, mappedBy="patientDischargeEpicrisis", orphanRemoval=true, cascade={"persist"})
     */
    private $dischargeEpicrisisFiles;

    public function __construct()
    {
        $this->dischargeEpicrisisFiles = new ArrayCollection();
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
     * @return Collection|DischargeEpicrisisFile[]
     */
    public function getDischargeEpicrisisFiles(): Collection
    {
        return $this->dischargeEpicrisisFiles;
    }

    /**
     * @param DischargeEpicrisisFile $dischargeEpicrisisFile
     *
     * @return $this
     */
    public function addDischargeEpicrisisFile(DischargeEpicrisisFile $dischargeEpicrisisFile): self
    {
        if (!$this->dischargeEpicrisisFiles->contains($dischargeEpicrisisFile)) {
            $this->dischargeEpicrisisFiles[] = $dischargeEpicrisisFile;
            $dischargeEpicrisisFile->setPatientDischargeEpicrisis($this);
        }
        return $this;
    }

    /**
     * @param DischargeEpicrisisFile $dischargeEpicrisisFile
     *
     * @return $this
     */
    public function removeDischargeEpicrisisFile(DischargeEpicrisisFile $dischargeEpicrisisFile): self
    {
        if ($this->dischargeEpicrisisFiles->contains($dischargeEpicrisisFile)) {
            $this->dischargeEpicrisisFiles->removeElement($dischargeEpicrisisFile);
            // set the owning side to null (unless already changed)
            if ($dischargeEpicrisisFile->getPatientDischargeEpicrisis() === $this) {
                $dischargeEpicrisisFile->setPatientDischargeEpicrisis(null);
            }
        }
        return $this;
    }
}
