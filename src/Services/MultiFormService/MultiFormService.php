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
     * @throws ReflectionException
     */
    public function generateForm(FormBuilderInterface $formBuilder, array $formDataArray): FormInterface
    {
        $formBuilder->setData($this->getFormBuilderData($formDataArray));
        /** @var FormData $formData */
        foreach ($formDataArray as $formData) {
            $formBuilder->add(
                self::getFormName($formData->getFormClassName(), $formData->getFormPostfix()),
                $formData->getFormClassName(),
                $formData->getFormOptions()
            );
        }
        return $formBuilder->getForm();
    }

    /**
     * Get data array for FormBuilder
     * @param array $formDataArray
     * @return array
     * @throws ReflectionException
     */
    public function getFormBuilderData(array $formDataArray): array
    {
        $builderDataArray = [];
        /** @var FormData $formData */
        foreach ($formDataArray as $formData) {
            if ($formData->getIsAddFormData()) {
                $builderDataArray[
                    self::getFormName($formData->getFormClassName(), $formData->getFormPostfix())
                ] = $formData->getEntity();
            }
        }
        return $builderDataArray;
    }

    /**
     * Add custom array of form options to even FormData object in array
     * @param array $formDataArray
     * @param array $mergeOptionsArray
     * @return MultiFormService
     */
    public function mergeFormDataOptions(array $formDataArray, array $mergeOptionsArray): self
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
        return $this;
    }

    /** Returns form name by class name
     * @param string $formClassName
     * @param int|null $postfix
     * @return string|string[]
     * @throws ReflectionException
     */
    public static function getFormName(string $formClassName, ?int $postfix = null)
    {
        return self::addFormPostfix(
            str_replace('Type', '', lcfirst((new ReflectionClass($formClassName))->getShortName())),
            $postfix
        );
    }

    /**
     * Adds custom string after form name
     * @param string $formName
     * @param int|null $postfix
     * @return string
     */
    public static function addFormPostfix(string $formName, ?int $postfix = null): string
    {
        $formName .= $postfix ? '_' .(string)$postfix : '';
        return $formName;
    }
}