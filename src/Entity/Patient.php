<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Пациент
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 * @ORM\Table(options={"comment":"Пациент"});
 * @UniqueEntity(
 *     fields={"snils"},
 *     ignoreNull="true",
 *     message="Такой СНИЛС уже существует!"
 * )
 * * @UniqueEntity(
 *     fields={"passport"},
 *     ignoreNull="true",
 *     message="Такие паспортные данные уже существуют!"
 * )
 * * * @UniqueEntity(
 *     fields={"insuranceNumber"},
 *     ignoreNull="true",
 *     message="Такой номер страхового полиса уже существует!"
 * )
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ пациента"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AuthUser", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $AuthUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital", inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hospital;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Адрес пациента"})
     */
    private $address;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг оповещения через смс", "default"=true})
     */
    private $smsInforming;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Флаг оповещения через email", "default"=true})
     */
    private $emailInforming;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Diagnosis")
     */
    private $diagnosis;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, options={"comment"="СНИЛС пациента"})
     */
    private $snils;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"comment"="Номер страховки"})
     */
    private $insuranceNumber;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, options={"comment"="Паспортный данные"})
     */
    private $passport;

    /**
     * @ORM\Column(type="integer", nullable=true, columnDefinition="INTEGER CHECK (weight >= 28)", options={"comment"="Вес"})
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true, columnDefinition="INTEGER CHECK (height >= 48)", options={"comment"="Рост"})
     */
    private $height;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата рождения"})
     */
    private $dateBirth;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\District", inversedBy="patients")
     */
    private $district;

    /**
     * @ORM\OneToMany(targetEntity=MedicalHistory::class, mappedBy="patient", orphanRemoval=true)
     */
    private $medicalHistories;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment"="Дата выдачи паспорта"})
     */
    private $passportIssueDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="Орган, выдавший паспорт"})
     */
    private $passportIssuingAuthority;

    /**
     * @ORM\Column(type="string", length=7, nullable=true, options={"comment"="Код органа, выдавшего паспорт"})
     */
    private $passportIssuingAuthorityCode;

    /**
     * Patient constructor.
     */
    public function __construct()
    {
        $this->diagnosis = new ArrayCollection();
        $this->medicalHistories = new ArrayCollection();
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
     * @return string|null
     */
    public function getSnils(): ?string
    {
        return $this->snils;
    }

    /**
     * @param string $snils
     *
     * @return $this
     */
    public function setSnils(string $snils): self
    {
        $this->snils = $snils;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInsuranceNumber(): ?string
    {
        return $this->insuranceNumber;
    }

    /**
     * @param string|null $insuranceNumber
     *
     * @return $this
     */
    public function setInsuranceNumber(?string $insuranceNumber): self
    {
        $this->insuranceNumber = $insuranceNumber;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateBirth(): ?DateTimeInterface
    {
        return $this->dateBirth;
    }

    /**
     * @param DateTimeInterface|null $dateBirth
     *
     * @return $this
     */
    public function setDateBirth(?DateTimeInterface $dateBirth): self
    {
        $this->dateBirth = $dateBirth;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSmsInforming(): ?bool
    {
        return $this->smsInforming;
    }

    /**
     * @param bool $smsInforming
     *
     * @return $this
     */
    public function setSmsInforming(bool $smsInforming): self
    {
        $this->smsInforming = $smsInforming;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEmailInforming(): ?bool
    {
        return $this->emailInforming;
    }

    /**
     * @param bool $emailInforming
     *
     * @return $this
     */
    public function setEmailInforming(bool $emailInforming): self
    {
        $this->emailInforming = $emailInforming;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassport(): ?string
    {
        return $this->passport;
    }

    /**
     * @param string $passport
     *
     * @return $this
     */
    public function setPassport(string $passport): self
    {
        $this->passport = $passport;
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
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return $this
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return District|null
     */
    public function getDistrict(): ?District
    {
        return $this->district;
    }

    /**
     * @param District|null $district
     *
     * @return $this
     */
    public function setDistrict(?District $district): self
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @return Collection|Diagnosis[]
     */
    public function getDiagnosis(): Collection
    {
        return $this->diagnosis;
    }

    /**
     * @param Diagnosis $diagnosis
     *
     * @return $this
     */
    public function addDiagnosi(Diagnosis $diagnosis): self
    {
        if (!$this->diagnosis->contains($diagnosis)) {
            $this->diagnosis[] = $diagnosis;
        }
        return $this;
    }

    /**
     * @param Diagnosis $diagnosis
     *
     * @return $this
     */
    public function removeDiagnosi(Diagnosis $diagnosis): self
    {
        if ($this->diagnosis->contains($diagnosis)) {
            $this->diagnosis->removeElement($diagnosis);
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int|null $weight
     *
     * @return $this
     */
    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return $this
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return Collection|MedicalHistory[]
     */
    public function getMedicalHistories(): Collection
    {
        return $this->medicalHistories;
    }

    /**
     * @param MedicalHistory $medicalHistory
     *
     * @return $this
     */
    public function addMedicalHistory(MedicalHistory $medicalHistory): self
    {
        if (!$this->medicalHistories->contains($medicalHistory)) {
            $this->medicalHistories[] = $medicalHistory;
            $medicalHistory->setPatient($this);
        }
        return $this;
    }

    /**
     * @param MedicalHistory $medicalHistory
     *
     * @return $this
     */
    public function removeMedicalHistory(MedicalHistory $medicalHistory): self
    {
        if ($this->medicalHistories->contains($medicalHistory)) {
            $this->medicalHistories->removeElement($medicalHistory);
            // set the owning side to null (unless already changed)
            if ($medicalHistory->getPatient() === $this) {
                $medicalHistory->setPatient(null);
            }
        }
        return $this;
    }

    public function getPassportIssueDate(): ?DateTimeInterface
    {
        return $this->passportIssueDate;
    }

    public function setPassportIssueDate(?DateTimeInterface $passportIssueDate): self
    {
        $this->passportIssueDate = $passportIssueDate;

        return $this;
    }

    public function getPassportIssuingAuthority(): ?string
    {
        return $this->passportIssuingAuthority;
    }

    public function setPassportIssuingAuthority(?string $passportIssuingAuthority): self
    {
        $this->passportIssuingAuthority = $passportIssuingAuthority;

        return $this;
    }

    public function getPassportIssuingAuthorityCode(): ?string
    {
        return $this->passportIssuingAuthorityCode;
    }

    public function setPassportIssuingAuthorityCode(?string $passportIssuingAuthorityCode): self
    {
        $this->passportIssuingAuthorityCode = $passportIssuingAuthorityCode;

        return $this;
    }
}
