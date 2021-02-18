<?php

namespace App\Entity;

use App\Repository\ClinicalDiagnosisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * Class ClinicalDiagnosis
 * @ORM\Entity(repositoryClass=ClinicalDiagnosisRepository::class)
 * @ORM\Table(options={"comment":"Клинический диагноз"});
 * @package App\Entity
 */
class ClinicalDiagnosis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ клинического диагноза"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", options={"comment"="Текст клинического диагноза"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $MKBCode;

    /**
     * Основное заболевание
     * @ORM\ManyToOne(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mainDisease;

    /**
     * Фоновые заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=true)
     * @JoinTable(name="background_diseases")
     */
    private $backgroundDiseases;

    /**
     * Осложнения основного заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=true)
     * @JoinTable(name="complications")
     */
    private $complications;

    /**
     * Сопутствующие заболевания
     * @ORM\ManyToMany(targetEntity=Diagnosis::class)
     * @ORM\JoinColumn(nullable=true)
     * @JoinTable(name="concomitant_diseases")
     */
    private $concomitantDiseases;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     * @ORM\JoinColumn(nullable=false)
     */
    private $enabled;

    /**
     * ClinicalDiagnosis constructor.
     */
    public function __construct()
    {
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
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Diagnosis|null
     */
    public function getMKBCode(): ?Diagnosis
    {
        return $this->MKBCode;
    }

    /**
     * @param Diagnosis|null $MKBCode
     * @return $this
     */
    public function setMKBCode(?Diagnosis $MKBCode): self
    {
        $this->MKBCode = $MKBCode;

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
     * @param Diagnosis|null $mainDisease
     * @return $this
     */
    public function setMainDisease(?Diagnosis $mainDisease): self
    {
        $this->mainDisease = $mainDisease;

        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getBackgroundDiseases(): Collection
    {
        return $this->backgroundDiseases;
    }

    /**
     * @param Diagnosis $backgroundDisease
     * @return $this
     */
    public function addBackgroundDisease(Diagnosis $backgroundDisease): self
    {
        if (!$this->backgroundDiseases->contains($backgroundDisease)) {
            $this->backgroundDiseases[] = $backgroundDisease;
        }

        return $this;
    }

    /**
     * @param Diagnosis $backgroundDisease
     * @return $this
     */
    public function removeBackgroundDisease(Diagnosis $backgroundDisease): self
    {
        $this->backgroundDiseases->removeElement($backgroundDisease);

        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getComplications(): Collection
    {
        return $this->complications;
    }

    /**
     * @param Diagnosis $complication
     * @return $this
     */
    public function addComplication(Diagnosis $complication): self
    {
        if (!$this->complications->contains($complication)) {
            $this->complications[] = $complication;
        }

        return $this;
    }

    /**
     * @param Diagnosis $complication
     * @return $this
     */
    public function removeComplication(Diagnosis $complication): self
    {
        $this->complications->removeElement($complication);

        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getConcomitantDiseases(): Collection
    {
        return $this->concomitantDiseases;
    }

    /**
     * @param Diagnosis $concomitantDisease
     * @return $this
     */
    public function addConcomitantDisease(Diagnosis $concomitantDisease): self
    {
        if (!$this->concomitantDiseases->contains($concomitantDisease)) {
            $this->concomitantDiseases[] = $concomitantDisease;
        }

        return $this;
    }

    /**
     * @param Diagnosis $concomitantDisease
     * @return $this
     */
    public function removeConcomitantDisease(Diagnosis $concomitantDisease): self
    {
        $this->concomitantDiseases->removeElement($concomitantDisease);

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
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
