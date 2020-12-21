<?php

namespace App\Services\Template;

/**
 * Class TemplateFilter
 *
 * @package App\Services\Template
 */
class TemplateFilter
{
    /** @var string $templateFilterName */
    private $templateFilterName;

    /** @var string $entityClass */
    private $entityClass;

    /** @var array $formData */
    private $formData;

    /** @var string|null $filterName */
    private $filterName;

    /**
     * TemplateFilter constructor.
     *
     * @param string $templateFilterName
     * @param string $entityClass
     * @param array $formData
     * @param string|null $filterName
     */
    public function __construct(string $templateFilterName, string $entityClass, array $formData, ?string $filterName = null)
    {
        $this->templateFilterName = $templateFilterName;
        $this->entityClass = $entityClass;
        $this->formData = $formData;
        $this->filterName = $filterName;
    }

    /**
     * @return string
     */
    public function getTemplateFilterName(): string
    {
        return $this->templateFilterName;
    }

    /**
     * @param string $templateFilterName
     *
     * @return $this
     */
    public function setTemplateFilterName(string $templateFilterName): self
    {
        $this->templateFilterName = $templateFilterName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     *
     * @return $this
     */
    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @param array $formData
     *
     * @return $this
     */
    public function setFormData(array $formData): self
    {
        $this->formData = $formData;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilterName(): ?string
    {
        return $this->filterName;
    }

    /**
     * @param string|null $filterName
     *
     * @return $this
     */
    public function setFilterName(?string $filterName): self
    {
        $this->filterName = $filterName;
        return $this;
    }
}