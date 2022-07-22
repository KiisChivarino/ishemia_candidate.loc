<?php

namespace App\Form\PatientAppointment;

use App\Controller\AppAbstractController;
use App\Entity\PatientAppointment;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientAppointmentConfirmedByStaffType extends AbstractType
{
    /** @var string Name of objective status text option */
    public const OBJECTIVE_STATUS_TEXT_OPTION_NAME = 'objectiveStatusText';

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
                'isProcessedByStaff', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('isProcessedByStaff'),
                    'required' => false,
                    'attr' => ['class' => 'isProcessedByStaffType'],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => PatientAppointment::class,
                self::OBJECTIVE_STATUS_TEXT_OPTION_NAME => null
            ])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
