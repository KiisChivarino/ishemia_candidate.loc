<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\DateInterval;
use App\Entity\TimeRange;
use App\Services\TemplateItems\FormTemplateItem;
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('title', TextType::class, ['label' => $templateItem->getContentValue('rangeTitle')])
            ->add('dateInterval', EntityType::class, [
                'label' => $templateItem->getContentValue('dateInterval'),
                'class' => DateInterval::class,
                'choice_label' => 'title',
            ])
            ->add('multiplier', IntegerType::class, [
                'label' => $templateItem->getContentValue('multiplier'),
                'attr' => ['min' => '1'],
            ])
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
