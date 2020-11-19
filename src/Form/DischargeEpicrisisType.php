<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\DischargeEpicrisisFile;
use App\Entity\PatientDischargeEpicrisis;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateItems\FormTemplateItem;
use ReflectionException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DischargeEpicrisisType
 *
 * @package App\Form
 */
class DischargeEpicrisisType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws ReflectionException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                MultiFormService::getFormName(DischargeEpicrisisFile::class) . 's', CollectionType::class, [
                    'entry_type' => DischargeEpicrisisFileType::class,
                    'prototype' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientDischargeEpicrisis::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}