<?php

namespace App\Form\Admin\MedicalHistory;

use App\Controller\AjaxController;
use App\Controller\AppAbstractController;
use App\Entity\Diagnosis;
use App\Entity\MedicalHistory;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class MainDiseaseType
 *
 * @package App\Form\Admin\MedicalHistory
 */
class MainDiseaseType extends AbstractType
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
                'clinicalDiagnosis', TextareaType::class, [
                    'label' => $templateItem->getContentValue('clinicalDiagnosis'),
                    'required' => true,
                    'attr' => ['class' => 'tinymce'],
                ]
            )
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
                    'required' => false
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => MedicalHistory::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}