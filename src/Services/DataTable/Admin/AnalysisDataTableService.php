<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Analysis;
use App\Entity\AnalysisGroup;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class AnalysisDataTableService extends AdminDatatableService
{
    /**
     * Таблица анализов в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name')
                ]
            )
            ->add(
                'description', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('description'),
                ]
            )
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, Analysis $analysis) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $analysis->getAnalysisGroup();
                        return
                            $analysisGroup ?
                                $this->getLink(
                                    $analysisGroup->getName(), $analysisGroup->getId(),
                                    'analysis_group_show'
                                )
                                : '';
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var AnalysisGroup $analysisGroup */
        $analysisGroup = $filters[AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Analysis::class,
                    'query' => function (QueryBuilder $builder) use ($analysisGroup) {
                        $builder
                            ->select('a')
                            ->from(Analysis::class, 'a');
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