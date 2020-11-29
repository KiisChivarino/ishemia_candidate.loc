<?php

namespace App\Form\Admin\PlanTesting;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\PlanTesting;
use App\Entity\TimeRange;
use App\Repository\AnalysisGroupRepository;
use App\Repository\TimeRangeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PlanTestingType
 * form of creating/editing plan testing
 *
 * @package App\Form\Admin\PlanTesting
 */
class PlanTestingType extends AbstractType
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
                    'attr' => ['min' => '0']
                ]
            )
            ->add(
                'analysisGroup', EntityType::class, [
                    'label' => $templateItem->getContentValue('analysisGroup'),
                    'class' => AnalysisGroup::class,
                    'choice_label' => 'name',
                    'query_builder' => function (AnalysisGroupRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.enabled = true');
                    },
                ]
            )
            ->add(
                'analysisGroup', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('analysisGroup'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_analysis_group_ajax',
                    'class' => AnalysisGroup::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 0,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('analysisGroupPlaceholder'),
                    'remote_params' => ['analysisGroup' => '0'],
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
            ->setDefaults(['data_class' => PlanTesting::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
