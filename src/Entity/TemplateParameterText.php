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

    /**
     * @ORM\OneToMany(targetEntity=TemplateManyToManyTemplateParameterText::class, mappedBy="templateParameterText")
     */
    private $templateManyToManyTemplateParameterTexts;

    /**
     * TemplateParameterText constructor.
     */
    public function __construct()
    {
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
     * @return TemplateParameter|null
     */
    public function getTemplateParameter(): ?TemplateParameter
    {
        return $this->templateParameter;
    }

    /**
     * @param TemplateParameter|null $templateParameter
     * @return $this
     */
    public function setTemplateParameter(?TemplateParameter $templateParameter): self
    {
        $this->templateParameter = $templateParameter;
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
            $templateManyToManyTemplateParameterText->setTemplateParameterText($this);
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
            if ($templateManyToManyTemplateParameterText->getTemplateParameterText() === $this) {
                $templateManyToManyTemplateParameterText->setTemplateParameterText(null);
            }
        }
        return $this;
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

}
