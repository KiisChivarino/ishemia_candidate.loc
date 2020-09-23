<?php

namespace App\Form\Admin\AnalysisRate;

use App\Controller\AppAbstractController;
use App\Entity\Analysis;
use App\Entity\AnalysisGroup;
use App\Entity\AnalysisRate;
use App\Entity\Measure;
use App\Entity\Period;
use App\Repository\AnalysisRepository;
use App\Repository\MeasureRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class AnalysisRateType
 * edit form of Analysis Rate
 *
 * @package App\Form\Admin\AnalysisRate
 */
class AnalysisRateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AnalysisGroup $groupFilter */
        $groupFilter = $options[AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']] ?? null;
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'analysis', EntityType::class, [
                    'label' => $templateItem->getContentValue('analysis'),
                    'class' => Analysis::class,
                    'choice_label' => function ($analysis) {
                        return
                            'Группа: '.$analysis->getAnalysisGroup()->getName().
                            ', Анализ: '.$analysis->getName();
                    },
                    'query_builder' => function (AnalysisRepository $er) use ($groupFilter) {
                        $qb = $er->createQueryBuilder('d')
                            ->where('d.enabled = true')
                            ->orderBy('d.analysisGroup')
                            ->orderBy('d.id');
                        if ($groupFilter) {
                            $qb->andWhere('d.analysisGroup = :ag')
                                ->setParameter('ag', $groupFilter);
                        }
                        return $qb;
                    },
                ]
            )
            ->add(
                'measure', EntityType::class, [
                    'label' => $templateItem->getContentValue('measure'),
                    'class' => Measure::class,
                    'choice_label' => 'nameRu',
                    'query_builder' => function (MeasureRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.enabled = true');
                    },
                ]
            )
            ->add(
                'period', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('period'),
                    'multiple' => false,
                    'remote_route' => 'find_period_ajax',
                    'class' => Period::class,
                    'primary_key' => 'id',
                    'text_property' => 'title',
                    'minimum_input_length' => 2,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => 'Выберите период',
                ]
            )
            ->add(
                'rateMin', NumberType::class, ['label' => $templateItem->getContentValue('rateMin'),]
            )
            ->add(
                'rateMax', NumberType::class, ['label' => $templateItem->getContentValue('rateMax'),]
            )
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AnalysisRate::class,])
            ->setDefined([AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']])
            ->setAllowedTypes(
                AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP'], [
                    AnalysisGroup::class,
                    'string'
                ]
            )
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
