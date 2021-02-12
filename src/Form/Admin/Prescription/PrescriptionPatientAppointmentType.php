<?php

namespace App\Form\Admin\Prescription;

use App\Controller\AjaxController;
use App\Controller\AppAbstractController;
use App\Entity\Complaint;
use App\Entity\PatientAppointment;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
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
class PrescriptionPatientAppointmentType extends AbstractType
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
                    'required' => false,
                    'empty_data' => null,
                    'by_reference' => true,
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
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                    'required' => false
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
                    'data' => $options[self::OBJECTIVE_STATUS_TEXT_OPTION_NAME] ?
                        $options[self::OBJECTIVE_STATUS_TEXT_OPTION_NAME]->getText()
                        : null,
                    'mapped' => false
                ]
            )
            ->add(
                'therapy', null, [
                    'label' => $templateItem->getContentValue('therapy'),
                    'attr' => ['class' => 'tinymce'],
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