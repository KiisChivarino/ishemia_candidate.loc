<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\DateInterval;
use App\Entity\TimeRange;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TimeRangeType
 *
 * @package App\Form\Admin
 */
class TimeRangeType extends AbstractType
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
            ->add('title', TextType::class, [
                'label' => $templateItem->getContentValue('rangeTitle'),
                'help' => $templateItem->getContentValue('rangeTitleHelp'),
            ])
            ->add('dateInterval', EntityType::class, [
                'label' => $templateItem->getContentValue('dateInterval'),
                'class' => DateInterval::class,
                'choice_label' => 'title',
                'help' => $templateItem->getContentValue('dateIntervalHelp'),
            ])
            ->add('multiplier', IntegerType::class, [
                'label' => $templateItem->getContentValue('multiplier'),
                'help' => $templateItem->getContentValue('multiplierHelp'),
                'attr' => ['min' => '1'],
            ])
            ->add(
                'isRegular',
                CheckboxType::class,
                [
                    'label' => $templateItem->getContentValue('isRegular'),
                    'required' => false,
                    'help' => $templateItem->getContentValue('isRegularHelp'),
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
            ->setDefaults(['data_class' => TimeRange::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
