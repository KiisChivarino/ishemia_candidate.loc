<?php

namespace App\Services\MultiFormService;

/**
 * Class FormData
 * creates object of data for generating form using formBuilder
 *
 * @package App\Services\ControllerGetters
 */
class FormData
{
    /** @var string $formTitle Title of form */
//    private $formTitle;

    /** @var object $entity Entity object */
    private $entity;

    /** @var string $formClassName Name of form class */
    private $formClassName;

    /** @var array $formOptions Options of form */
    private $formOptions;

    /** @var bool $isAddFormData Is add entity into form data array? */
    private $isAddFormData;

    /** @var int|null $formNamePostfix Numeric postfix for form, created in cycle */
    private $formNamePostfix;

    /**
     * FormData constructor.
     * @param object $entity
     * @param string $formClassName
     * @param array $formOptions
     * @param bool $isAddFormData
     * @param int|null $formNamePostfix
     */
    public function __construct(object $entity, string $formClassName, array $formOptions = [], bool $isAddFormData = true, int $formNamePostfix = null)
    {
        $this->entity = $entity;
        $this->formClassName = $formClassName;
        $this->formOptions = $formOptions;
        $this->isAddFormData = $isAddFormData;
        $this->formNamePostfix = $formNamePostfix;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getFormClassName(): string
    {
        return $this->formClassName;
    }

    /**
     * @return array
     */
    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    /**
     * @param array $formOptions
     * @return FormData
     */
    public function setFormOptions(array $formOptions): self
    {
        $this->formOptions = $formOptions;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAddFormData(): bool
    {
        return $this->isAddFormData;
    }

    /**
     * @param int $postfix
     * @return $this
     */
    public function setFormPostfix(int $postfix): self
    {
        $this->formNamePostfix = $postfix;
        return $this;
    }

    /**
     * @return int
     */
    public function getFormPostfix(): ?int
    {
        return $this->formNamePostfix;
    }
}