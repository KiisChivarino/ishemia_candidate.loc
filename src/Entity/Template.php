<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TemplateRepository::class)
 */
class Template
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ шаблона"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название шаблона"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateType::class, inversedBy="templates", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="SET NULL")
     */
    private $templateType;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=TextByTemplate::class, mappedBy="template", cascade={"persist"})
     */
    private $textByTemplates;

    /**
     * @ORM\OneToMany(targetEntity=TemplateManyToManyTemplateParameterText::class, mappedBy="template", cascade={"persist"})
     */
    private $templateManyToManyTemplateParameterTexts;

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->textByTemplates = new ArrayCollection();
        $this->templateManyToManyTemplateParameterTexts = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
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

    /**
     * @return Collection|TextByTemplate[]
     */
    public function getTextByTemplates(): Collection
    {
        return $this->textByTemplates;
    }

    /**
     * @param TextByTemplate $textByTemplate
     * @return $this
     */
    public function addTextByTemplate(TextByTemplate $textByTemplate): self
    {
        if (!$this->textByTemplates->contains($textByTemplate)) {
            $this->textByTemplates[] = $textByTemplate;
            $textByTemplate->setTemplate($this);
        }
        return $this;
    }

    /**
     * @param TextByTemplate $textByTemplate
     * @return $this
     */
    public function removeTextByTemplate(TextByTemplate $textByTemplate): self
    {
        if ($this->textByTemplates->contains($textByTemplate)) {
            $this->textByTemplates->removeElement($textByTemplate);
            // set the owning side to null (unless already changed)
            if ($textByTemplate->getTemplate() === $this) {
                $textByTemplate->setTemplate(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|TemplateManyToManyTemplateParameterText[]
     */
    public function getTemplateManyToManyTemplateParameterTexts(): Collection
    {
        return $this->templateManyToManyTemplateParameterTexts;
    }

    /**
     * @param TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText
     * @return $this
     */
    public function addTemplateManyToManyTemplateParameterText(TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText): self
    {
        if (!$this->templateManyToManyTemplateParameterTexts->contains($templateManyToManyTemplateParameterText)) {
            $this->templateManyToManyTemplateParameterTexts[] = $templateManyToManyTemplateParameterText;
            $templateManyToManyTemplateParameterText->setTemplate($this);
        }
        return $this;
    }

    /**
     * @param TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText
     * @return $this
     */
    public function removeTemplateManyToManyTemplateParameterText(TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText): self
    {
        if ($this->templateManyToManyTemplateParameterTexts->contains($templateManyToManyTemplateParameterText)) {
            $this->templateManyToManyTemplateParameterTexts->removeElement($templateManyToManyTemplateParameterText);
            // set the owning side to null (unless already changed)
            if ($templateManyToManyTemplateParameterText->getTemplate() === $this) {
                $templateManyToManyTemplateParameterText->setTemplate(null);
            }
        }
        return $this;
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
}
