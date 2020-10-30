<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Complaint;
use App\Entity\PatientAppointment;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PatientAppointmentType
 *
 * @package App\Form
 */
class PatientAppointmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'recommendation', null, [
                    'label' => $templateItem->getContentValue('recommendation'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add(
                'appointmentTime', DateTimeType::class, [
                    'label' => $templateItem->getContentValue('appointmentTime'),
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('appointmentTimeDateLabel'),
                    'time_widget' => 'single_text',
                    'time_label' => $templateItem->getContentValue('appointmentTimeTimeLabel'),
                ]
            )
            ->add(
                'complaints', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('complaints'),
                    'method' => 'POST',
                    'multiple' => true,
                    'remote_route' => 'find_complaint_ajax',
                    'class' => Complaint::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('complaintsPlaceholder'),
                    'attr' => ['class' => 'js-example-basic-single'],
                ]
            )
            ->add(
                'complaintsComment', null, [
                    'label' => $templateItem->getContentValue('complaintsComment'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add(
                'objectiveStatus', TextareaType::class, [
                    'label' => $templateItem->getContentValue('objectiveStatus'),
                    'attr' => ['class' => 'tinymce'],
                    'data' => $options['objectiveStatusText']->getText(),
                    'mapped' => false
                ]
            )
            ->add(
                'therapy', null, [
                    'label' => $templateItem->getContentValue('therapy'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
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
            ->setDefaults([
                'data_class' => PatientAppointment::class,
                'objectiveStatusText' => null
                ])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
