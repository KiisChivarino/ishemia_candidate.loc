<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Measure;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MeasureType
 * Форма единицы измерения
 *
 * @package App\Form\Admin
 */
class MeasureType extends AbstractType
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
            ->add('nameRu', null, [
                'label' => $templateItem->getContentValue('nameRu'),
                'help' => $templateItem->getContentValue('onlyRuss')
            ])
            ->add('nameEn', null, [
                'label' => $templateItem->getContentValue('nameEn'),
                'help' => $templateItem->getContentValue('onlyEng')
            ])
            ->add('title', null, ['label' => $templateItem->getContentValue('measureTitle')])
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
            ->setDefaults(['data_class' => Measure::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
