<?php

namespace App\Form\Admin;

use App\Controller\AjaxController;
use App\Controller\AppAbstractController;
use App\Entity\Diagnosis;
use App\Entity\MedicalHistory;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class MedicalHistoryType
 * edit/create form of medical history
 *
 * @package App\Form\Admin
 */
class MedicalHistoryType extends AbstractType
{
    /** @var string Name of life history form */
    public const FORM_LIFE_HISTORY_NAME = 'lifeHistory';

    /** @var string Key of anamnesis of life text option */
    public const ANAMNES_OF_LIFE_TEXT_OPTION_KEY = 'anamnesOfLifeText';

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
                'dateBegin', DateType::class, [
                    'label' => $templateItem->getContentValue('dateBegin'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ]
            )
            ->add(
                'backgroundDiseases', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('backgroundDiseases'),
                    'method' => 'POST',
                    'multiple' => true,
                    'remote_route' => 'find_diagnosis_ajax',
                    'class' => Diagnosis::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('backgroundDiseasesPlaceholder'),
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                ]
            )
            ->add(
                'complications', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('complications'),
                    'method' => 'POST',
                    'multiple' => true,
                    'remote_route' => 'find_diagnosis_ajax',
                    'class' => Diagnosis::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('complicationsPlaceholder'),
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                ]
            )
            ->add(
                'concomitantDiseases', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('concomitantDiseases'),
                    'method' => 'POST',
                    'multiple' => true,
                    'remote_route' => 'find_diagnosis_ajax',
                    'class' => Diagnosis::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('concomitantDiseasesPlaceholder'),
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                ]
            )
            ->add(
                'diseaseHistory', null, [
                    'label' => $templateItem->getContentValue('diseaseHistory'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add(
                self::FORM_LIFE_HISTORY_NAME, TextareaType::class, [
                    'label' => $templateItem->getContentValue('lifeHistory'),
                    'attr' => ['class' => 'tinymce'],
                    'data' => $options['anamnesOfLifeText'],
                    'mapped' => false
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
            ->setDefaults(['data_class' => MedicalHistory::class, 'anamnesOfLifeText' => null])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
