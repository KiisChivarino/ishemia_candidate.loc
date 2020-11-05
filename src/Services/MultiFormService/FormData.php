<?php

namespace App\Services\MultiFormService;

use ReflectionException;

/**
 * Class FormData
 * creates object of data for generating form using formBuilder
 *
 * @package App\Services\ControllerGetters
 */
class FormData
{
    /** @var string $formTitle Title of form */
    private $formTitle;

    /** @var object $entity Entity object */
    private $entity;

    /** @var string $formClassName Name of form class */
    private $formClassName;

    /** @var array $formOptions Options of form */
    private $formOptions;

    /** @var bool $isAddFormData Is add entity into form data array? */
    private $isAddFormData;

    /**
     * FormData constructor.
     * @param object $entity
     * @param string $formClassName
     * @param array $formOptions
     * @param bool $isAddFormData
     * @throws ReflectionException
     */
    public function __construct(object $entity, string $formClassName, array $formOptions = [], bool $isAddFormData = true)
    {
        $this->formTitle = MultiFormService::getFormName($formClassName);
        $this->entity = $entity;
        $this->formClassName = $formClassName;
        $this->formOptions = $formOptions;
        $this->isAddFormData = $isAddFormData;
    }

    /**
     * @return string
     */
    public function getFormTitle(): string
    {
        return $this->formTitle;
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
    public function getIsAddFormData()
    {
        return $this->isAddFormData;
    }
}