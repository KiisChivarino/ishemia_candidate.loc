<?php

namespace App\Entity;

use App\Repository\TemplateParameterTextRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TemplateParameterTextRepository::class)
 */
class TemplateParameterText
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment"="Ключ параметра шаблона"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="Текст параметра шаблона"})
     */
    private $text;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateParameter::class, inversedBy="templateParameterTexts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $templateParameter;

//    /**
//     * @ORM\ManyToMany(targetEntity=Template::class, mappedBy="templateParameterTexts")
//     */
//    private $templates;

    /**
     * @ORM\OneToMany(targetEntity=TemplateManyToManyTemplateParameterText::class, mappedBy="templateParameterText")
     */
    private $templateManyToManyTemplateParameterTexts;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
        $this->templateManyToManyTemplateParameterTexts = new ArrayCollection();
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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getTemplateParameter(): ?TemplateParameter
    {
        return $this->templateParameter;
    }

    public function setTemplateParameter(?TemplateParameter $templateParameter): self
    {
        $this->templateParameter = $templateParameter;

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
            $template->addTemplateParameterText($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->templates->contains($template)) {
            $this->templates->removeElement($template);
            $template->removeTemplateParameterText($this);
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

    public function addTemplateManyToManyTemplateParameterText(TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText): self
    {
        if (!$this->templateManyToManyTemplateParameterTexts->contains($templateManyToManyTemplateParameterText)) {
            $this->templateManyToManyTemplateParameterTexts[] = $templateManyToManyTemplateParameterText;
            $templateManyToManyTemplateParameterText->setTemplateParameterText($this);
        }

        return $this;
    }

    public function removeTemplateManyToManyTemplateParameterText(TemplateManyToManyTemplateParameterText $templateManyToManyTemplateParameterText): self
    {
        if ($this->templateManyToManyTemplateParameterTexts->contains($templateManyToManyTemplateParameterText)) {
            $this->templateManyToManyTemplateParameterTexts->removeElement($templateManyToManyTemplateParameterText);
            // set the owning side to null (unless already changed)
            if ($templateManyToManyTemplateParameterText->getTemplateParameterText() === $this) {
                $templateManyToManyTemplateParameterText->setTemplateParameterText(null);
            }
        }

        return $this;
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

}
