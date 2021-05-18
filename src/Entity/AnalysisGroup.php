<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalysisGroupRepository")
 * @ORM\Table(options={"comment":"Группа анализов"});
 */
class AnalysisGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", options={"comment"="Ключ группы анализов"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название группы анализов (аббревиатура)"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="Полное название группы анализов (расшифровка аббревиатуры)"})
     */
    private $fullName;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Analysis", mappedBy="analysisGroup")
     */
    private $analyses;

    /**
     * AnalysisGroup constructor.
     */
    public function __construct()
    {
        $this->analyses = new ArrayCollection();
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
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return $this
     */
    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;
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
     * @return Collection|Analysis[]
     */
    public function getAnalyses(): Collection
    {
        return $this->analyses->filter(
            function(Analysis $analysis) {
                return $analysis->getEnabled();
            }
        );
    }

    /**
     * @param Analysis $analysis
     * @return $this
     */
    public function addAnalysis(Analysis $analysis): self
    {
        if (!$this->analyses->contains($analysis)) {
            $this->analyses[] = $analysis;
            $analysis->setAnalysisGroup($this);
        }
        return $this;
    }

    /**
     * @param Analysis $analysis
     * @return $this
     */
    public function removeAnalysis(Analysis $analysis): self
    {
        if ($this->analyses->contains($analysis)) {
            $this->analyses->removeElement($analysis);
            // set the owning side to null (unless already changed)
            if ($analysis->getAnalysisGroup() === $this) {
                $analysis->setAnalysisGroup(null);
            }
        }
        return $this;
    }
}
