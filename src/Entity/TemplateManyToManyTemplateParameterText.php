<?php

namespace App\Entity;

use App\Repository\TemplateManyToManyTemplateParameterTextRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TemplateManyToManyTemplateParameterTextRepository::class)
 */
class TemplateManyToManyTemplateParameterText
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="templateManyToManyTemplateParameterTexts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $template;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateParameterText::class, inversedBy="templateManyToManyTemplateParameterTexts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="SET NULL")
     */
    private $templateParameterText;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTemplateParameterText(): ?TemplateParameterText
    {
        return $this->templateParameterText;
    }

    public function setTemplateParameterText(?TemplateParameterText $templateParameterText): self
    {
        $this->templateParameterText = $templateParameterText;

        return $this;
    }
}
