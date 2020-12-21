<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Analysis;
use App\Entity\AnalysisGroup;
use App\Entity\AnalysisRate;
use App\Entity\Measure;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class AnalysisRateDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class AnalysisRateDataTableService extends AdminDatatableService
{

    /**
     * Таблица референтных значений в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, AnalysisRate $analysisRate) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $analysisRate->getAnalysis()->getAnalysisGroup();
                        return
                            $analysisGroup ?
                                $this->getLink($analysisGroup->getName(), $analysisGroup->getId(), 'analysis_group_show')
                                : '';
                    },
                    'searchable' => true
                ]
            )
            ->add(
                'analysis', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysis'),
                    'render' => function (string $data, AnalysisRate $analysisRate) {
                        /** @var Analysis $analysis */
                        $analysis = $analysisRate->getAnalysis();
                        return
                            $analysis ?
                                $this->getLink($analysis->getName(), $analysis->getId(), 'analysis_show')
                                : '';
                    },
                    'searchable' => true
                ]
            )
            ->add(
                'rateMin', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('rateMin'),
                    'searchable' => false
                ]
            )
            ->add(
                'rateMax', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('rateMax'),
                    'searchable' => false
                ]
            )
            ->add(
                'gender', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('gender'),
                    'field' => 'g.name',
                    'searchable' => true
                ]
            )
            ->add(
                'measure', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('measure'),
                    'render' => function (string $data, AnalysisRate $analysisRate) {
                        /** @var Measure $measure */
                        $measure = $analysisRate->getMeasure();
                        return $measure ? $this->getLink($measure->getNameRu(), $measure->getId(), 'measure_show') : '';
                    },
                    'searchable' => true
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var AnalysisGroup $analysisGroup */
        $analysisGroup = $filters[AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => AnalysisRate::class,
                    'query' => function (QueryBuilder $builder) use ($analysisGroup) {
                        $builder
                            ->select('ar')
                            ->from(AnalysisRate::class, 'ar')
                            ->leftJoin('ar.gender', 'g')
                            ->leftJoin('ar.analysis', 'a');
                        if ($analysisGroup) {
                            $builder
                                ->andWhere('a.analysisGroup = :valAnalysisGroup')
                                ->setParameter('valAnalysisGroup', $analysisGroup);
                        }
                    },
                ]
            );
    }
}