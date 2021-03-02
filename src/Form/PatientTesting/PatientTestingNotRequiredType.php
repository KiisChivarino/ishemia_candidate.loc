<?php

namespace App\Form\PatientTesting;

use App\Controller\AppAbstractController;
use App\Entity\PatientTesting;
use App\Form\PatientTestingFileType;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientTestingNotRequiredType
 * Форма Тестирования пациента
 *
 * @package App\Form\Admin
 */
class PatientTestingNotRequiredType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'analysisDate',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('analysisDate'),
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'resultData', TextareaType::class, [
                    'label' => $templateItem->getContentValue('resultData'),
                    'required' => false,
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add(
                'isProcessedByStaff', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('processed'),
                    'required' => false,
                ]
            )
            ->add(
                'patientTestingFiles', CollectionType::class, [
                    'entry_type' => PatientTestingFileType::class,
                    'prototype' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                ]
            )
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientTesting::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
