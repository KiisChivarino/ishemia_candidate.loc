<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PatientSMS;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReceivedSMSType
 * @package App\Form\Admin
 */
class PatientSMSType extends AbstractType
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
            ->add('text', TextType::class, [
                'label' => $templateItem->getContentValue('text'),
                'required' => false,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('isProcessed', CheckboxType::class, [
                'label' => $templateItem->getContentValue('isProcessed'),
                'required' => false
            ])
            ->add('patient', null, [
                'label' => $templateItem->getContentValue('patient'),
                'required' => false,
                'mapped' => true,
                'disabled' => true
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientSMS::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
