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
    private $patient;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getPatient(): ?PatientAppointment
    {
        return $this->patient;
    }

    public function setPatient(?PatientAppointment $patient): self
    {
        $this->patient = $patient;

        // set (or unset) the owning side of the relation if necessary
        $newObjectiveStatus = null === $patient ? null : $this;
        if ($patient->getObjectiveStatus() !== $newObjectiveStatus) {
            $patient->setObjectiveStatus($newObjectiveStatus);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    public function setTemplateType(?TemplateType $templateType): self
    {
        $this->templateType = $templateType;

        return $this;
    }

}
