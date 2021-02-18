<?php

namespace App\Form\Admin\ClinicalDiagnosis;

use App\Controller\AjaxController;
use App\Controller\AppAbstractController;
use App\Entity\ClinicalDiagnosis;
use App\Entity\Diagnosis;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class ClinicalDiagnosisType
 * @package App\Form\Admin
 */
class ClinicalDiagnosisType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('text', TextareaType::class, [
                'required' => true,
                'label' => $templateItem->getContentValue('text'),
                'attr' => ['class' => 'tinymce'],
            ])
            ->add(
                'MKBCode', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('MKBCode'),
                    'method' => 'POST',
                    'remote_route' => 'find_diagnosis_ajax',
                    'class' => Diagnosis::class,
                    'primary_key' => 'id',
                    'text_property' => 'code',
                    'minimum_input_length' => 3,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('MKBCodePlaceholder'),
                    'attr' => ['class' => AjaxController::AJAX_INIT_CSS_CLASS],
                    'required' => false,
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => ClinicalDiagnosis::class])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
