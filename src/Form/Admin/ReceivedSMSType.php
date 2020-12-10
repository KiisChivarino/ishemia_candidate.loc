<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\ReceivedSMS;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceivedSMSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('created_at',DateTimeType::class, [
                'label' => $templateItem->getContentValue('created_at'),
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('text', TextType::class, [
                'label' => $templateItem->getContentValue('text'),
                'required' => false,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('isProcessed', CheckboxType::class, [
                'label' => $templateItem->getContentValue('processed'),
                'required' => true
            ])
            ->add('patient', TextType::class, [
                'label' => $templateItem->getContentValue('patient'),
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'readonly' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => ReceivedSMS::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
