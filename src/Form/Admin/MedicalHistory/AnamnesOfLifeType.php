<?php

namespace App\Form\Admin\MedicalHistory;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MedicalHistoryType
 * edit/create form of medical history
 *
 * @package App\Form\Admin
 */
class AnamnesOfLifeType extends AbstractType
{
    /** @var string Name of life history form */
    public const FORM_LIFE_HISTORY_NAME = 'lifeHistory';

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
                self::FORM_LIFE_HISTORY_NAME, TextareaType::class, [
                    'label' => $templateItem->getContentValue('lifeHistory'),
                    'attr' => ['class' => 'tinymce'],
                    'data' => $options['anamnesOfLifeText'],
                    'required'=>false,
                    'mapped' => false
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
