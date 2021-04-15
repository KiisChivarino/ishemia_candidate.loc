<?php

namespace App\Form\PrescriptionAppointmentType;

use App\Controller\AppAbstractController;
use App\Entity\PrescriptionAppointment;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionAppointmentType
 * @package App\Form
 */
class PrescriptionAppointmentPlannedDateType extends AbstractType
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
                'plannedDateTime', DateTimeType::class, [
                    'label' => $templateItem->getContentValue('plannedDateTime'),
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('plannedTimeDateLabel'),
                    'time_widget' => 'single_text',
                    'time_label' => $templateItem->getContentValue('plannedTimeTimeLabel'),
                    'input_format' => 'Y-m-d H:i',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PrescriptionAppointment::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}