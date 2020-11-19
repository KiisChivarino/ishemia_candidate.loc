<?php

namespace App\Services\MultiFormService;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class MultiFormService
 * for creating compound forms in controllers
 * @package App\Services\MultiFormService
 */
class MultiFormService
{
    /**
     * Generate form from array of FormData objects
     * @param FormBuilderInterface $formBuilder
     * @param array $formDataArray
     * @return FormInterface
     */
    public function generateForm(FormBuilderInterface $formBuilder, array $formDataArray): FormInterface
    {
        $formBuilder->setData($this->getFormBuilderData($formDataArray));
        /** @var FormData $formData */
        foreach ($formDataArray as $formData) {
            $formBuilder->add($formData->getFormTitle(), $formData->getFormClassName(), $formData->getFormOptions());
        }
        return $formBuilder->getForm();
    }

    /**
     * Get data array for FormBuilder
     * @param array $formDataArray
     * @return array
     */
    public function getFormBuilderData(array $formDataArray): array
    {
        $builderDataArray = [];
        /** @var FormData $formData */
        foreach ($formDataArray as $formData) {
            if ($formData->getIsAddFormData()) {
                $builderDataArray[$formData->getFormTitle()] = $formData->getEntity();
            }
        }
        return $builderDataArray;
    }

    /**
     * Add custom array of form options to even FormData object in array
     * @param array $formDataArray
     * @param array $mergeOptionsArray
     * @return array
     */
    public function mergeFormDataOptions(array $formDataArray, array $mergeOptionsArray): array
    {
        /** @var FormData $formData */
        foreach ($formDataArray as $formData) {
            $formData->setFormOptions(
                array_merge(
                    $formData->getFormOptions(),
                    $mergeOptionsArray
                )
            );
        }
        return $formDataArray;
    }

    /** Returns form name by class name
     * @param string $formClassName
     * @return string|string[]
     * @throws ReflectionException
     */
    public static function getFormName(string $formClassName)
    {
        return str_replace('Type', '', lcfirst((new ReflectionClass($formClassName))->getShortName()));
    }
}