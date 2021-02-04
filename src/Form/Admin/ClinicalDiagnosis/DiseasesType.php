<?php

namespace App\Form\Admin\ClinicalDiagnosis;

use App\Controller\AjaxController;
use App\Controller\AppAbstractController;
use App\Entity\ClinicalDiagnosis;
use App\Entity\Diagnosis;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class DiseasesType
 * @package App\Form\Admin\ClinicalDiagnosis
 */
class DiseasesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'mainDisease', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('mainDisease'),
                    'method' => 'POST',
                    'remote_route' => 'find_diagnosis_ajax',
                    'class' => Diagnosis::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('mainDiseasePlaceholder'),
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => ClinicalDiagnosis::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}