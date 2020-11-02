<?php

namespace App\Entity;

use App\Repository\TemplateParameterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TemplateParameterRepository::class)
 */
class TemplateParameter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ параметра типа шаблона"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название параметра типа шаблона"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateType::class, inversedBy="templateParameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $templateType;

    /**
     * @ORM\OneToMany(targetEntity=TemplateParameterText::class, mappedBy="templateParameter")
     */
    private $templateParameterTexts;

    public function __construct()
    {
        $this->templateParameterTexts = new ArrayCollection();
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

    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    public function setTemplateType(?TemplateType $templateType): self
    {
        $this->templateType = $templateType;

        return $this;
    }

    /**
     * @return Collection|TemplateParameterText[]
     */
    public function getTemplateParameterTexts(): Collection
    {
        return $this->templateParameterTexts;
    }

    public function addTemplateParameterText(TemplateParameterText $templateParameterText): self
    {
        if (!$this->templateParameterTexts->contains($templateParameterText)) {
            $this->templateParameterTexts[] = $templateParameterText;
            $templateParameterText->setTemplateParameter($this);
        }

        return $this;
    }

    public function removeTemplateParameterText(TemplateParameterText $templateParameterText): self
    {
        if ($this->templateParameterTexts->contains($templateParameterText)) {
            $this->templateParameterTexts->removeElement($templateParameterText);
            // set the owning side to null (unless already changed)
            if ($templateParameterText->getTemplateParameter() === $this) {
                $templateParameterText->setTemplateParameter(null);
            }
        }

        return $this;
    }
}
