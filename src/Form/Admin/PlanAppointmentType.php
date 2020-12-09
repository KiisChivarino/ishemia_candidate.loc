<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PlanAppointment;
use App\Entity\StartingPoint;
use App\Entity\TimeRange;
use App\Repository\TimeRangeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlanAppointmentType
 *
 * @package App\Form\Admin
 */
class PlanAppointmentType extends AbstractType
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
                'timeRange', EntityType::class,
                [
                    'label' => $templateItem->getContentValue('timeRange'),
                    'class' => TimeRange::class,
                    'choice_label' => 'title',
                    'query_builder' => function (TimeRangeRepository $er) {
                        return $er->createQueryBuilder('tr')
                            ->where('tr.enabled = true');
                    },
                ]
            )
            ->add(
                'timeRangeCount', IntegerType::class,
                [
                    'label' => $templateItem->getContentValue('timeRangeCount'),
                    'attr' => ['min' => '1']
                ]
            )
            ->add(
                'startingPoint', EntityType::class, [
                    'label' => $templateItem->getContentValue('startingPoint'),
                    'class' => StartingPoint::class,
                    'choice_label' => 'title',
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
            ->setDefaults(['data_class' => PlanAppointment::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
