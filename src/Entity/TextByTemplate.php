<?php

namespace App\Entity;

use App\Repository\TextByTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TextByTemplateRepository::class)
 */
class TextByTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ текста шаблона"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Оригинальный текст по шаблону"})
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="textByTemplates")
     * @ORM\JoinColumn(nullable=true)
     */
    private $template;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateType::class, inversedBy="textByTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $templateType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PatientAppointment", mappedBy="objectiveStatus")
     */
    private $patientAppointment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MedicalHistory", mappedBy="lifeHistory")
     */
    private $lifeHistory;

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
     * @param string|null $text
     * @return $this
     */
    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return Template|null
     */
    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    /**
     * @param Template|null $template
     * @return $this
     */
    public function setTemplate(?Template $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return PatientAppointment|null
     */
    public function getPatientAppointment(): ?PatientAppointment
    {
        return $this->patientAppointment;
    }

    /**
     * @param PatientAppointment|null $patientAppointment
     * @return $this
     */
    public function setPatient(?PatientAppointment $patientAppointment): self
    {
        $this->patientAppointment = $patientAppointment;
        // set (or unset) the owning side of the relation if necessary
        $newObjectiveStatus = null === $patientAppointment ? null : $this;
        if ($patientAppointment->getObjectiveStatus() !== $newObjectiveStatus) {
            $patientAppointment->setObjectiveStatus($newObjectiveStatus);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * @return TemplateType|null
     */
    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    /**
     * @param TemplateType|null $templateType
     * @return $this
     */
    public function setTemplateType(?TemplateType $templateType): self
    {
        $this->templateType = $templateType;
        return $this;
    }

    /**
     * @return MedicalHistory|null
     */
    public function getLifeHistory(): ?MedicalHistory
    {
        return $this->lifeHistory;
    }

    /**
     * @param MedicalHistory|null $lifeHistory
     * @return $this
     */
    public function setLifeHistory(?MedicalHistory $lifeHistory): self
    {
        $this->lifeHistory = $lifeHistory;
        // set (or unset) the owning side of the relation if necessary
        $newLifeHistory = null === $lifeHistory ? null : $this;
        if ($lifeHistory->getLifeHistory() !== $newLifeHistory) {
            $lifeHistory->setLifeHistory($newLifeHistory);
        }
        return $this;
    }

    public function setPatientAppointment(?PatientAppointment $patientAppointment): self
    {
        // unset the owning side of the relation if necessary
        if ($patientAppointment === null && $this->patientAppointment !== null) {
            $this->patientAppointment->setObjectiveStatus(null);
        }

        // set the owning side of the relation if necessary
        if ($patientAppointment !== null && $patientAppointment->getObjectiveStatus() !== $this) {
            $patientAppointment->setObjectiveStatus($this);
        }

        $this->patientAppointment = $patientAppointment;

        return $this;
    }

}
