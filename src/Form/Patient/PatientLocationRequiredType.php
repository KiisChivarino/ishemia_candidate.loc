<?php

namespace App\Form\Patient;

use App\Controller\AppAbstractController;
use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PatientLocationRequiredType
 * form items for input hospital and city or other geographic fields for patient
 * if doctor`s role is DOCTOR_HOSPITAL don`t add this form: add hospital and city of hospital doctor
 * @package App\Form\Patient
 */
class PatientLocationRequiredType extends AbstractType
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
                'city', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('city'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_city_ajax',
                    'class' => City::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 2,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('cityPlaceholder'),
                    'required' => true
                ]
            )
            ->add(
                'hospital', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('hospital'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_hospital_ajax',
                    'class' => Hospital::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 0,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('hospitalPlaceholder'),
                    'remote_params' => ['city' => '0'],
                    'required' => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Patient::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}