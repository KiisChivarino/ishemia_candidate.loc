<?php

namespace App\Entity;

use App\Repository\TemplateTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TemplateTypeRepository::class)
 */
class TemplateType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ типа шаблона"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название шаблона"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=TemplateParameter::class, mappedBy="templateType")
     */
    private $templateParameters;

    /**
     * @ORM\OneToMany(targetEntity=Template::class, mappedBy="templateType")
     */
    private $templates;

    /**
     * @ORM\OneToMany(targetEntity=TextByTemplate::class, mappedBy="templateType")
     */
    private $textByTemplates;

    public function __construct()
    {
        $this->templateParameters = new ArrayCollection();
        $this->templates = new ArrayCollection();
        $this->textByTemplates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|TemplateParameter[]
     */
    public function getTemplateParameters(): Collection
    {
        return $this->templateParameters;
    }

    public function __toString(): ?string
    {
        return $this->getName();
    }

    public function addTemplateParameter(TemplateParameter $templateParameter): self
    {
        if (!$this->templateParameters->contains($templateParameter)) {
            $this->templateParameters[] = $templateParameter;
            $templateParameter->setTemplateType($this);
        }

        return $this;
    }

    public function removeTemplateParameter(TemplateParameter $templateParameter): self
    {
        if ($this->templateParameters->contains($templateParameter)) {
            $this->templateParameters->removeElement($templateParameter);
            // set the owning side to null (unless already changed)
            if ($templateParameter->getTemplateType() === $this) {
                $templateParameter->setTemplateType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Template[]
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function addTemplate(Template $template): self
    {
        if (!$this->templates->contains($template)) {
            $this->templates[] = $template;
            $template->setTemplateType($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->templates->contains($template)) {
            $this->templates->removeElement($template);
            // set the owning side to null (unless already changed)
            if ($template->getTemplateType() === $this) {
                $template->setTemplateType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TextByTemplate[]
     */
    public function getTextByTemplates(): Collection
    {
        return $this->textByTemplates;
    }

    public function addTextByTemplate(TextByTemplate $textByTemplate): self
    {
        if (!$this->textByTemplates->contains($textByTemplate)) {
            $this->textByTemplates[] = $textByTemplate;
            $textByTemplate->setTemplateType($this);
        }

        return $this;
    }

    public function removeTextByTemplate(TextByTemplate $textByTemplate): self
    {
        if ($this->textByTemplates->contains($textByTemplate)) {
            $this->textByTemplates->removeElement($textByTemplate);
            // set the owning side to null (unless already changed)
            if ($textByTemplate->getTemplateType() === $this) {
                $textByTemplate->setTemplateType(null);
            }
        }

        return $this;
    }
}
