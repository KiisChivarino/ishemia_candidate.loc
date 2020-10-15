<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\PlanTesting;
use App\Entity\TimeRange;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PlanTestingDataTableService
 * the table of list plan testing
 *
 * @package App\Services\DataTable\Admin
 */
class PlanTestingDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, PlanTesting $planTesting) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $planTesting->getAnalysisGroup();
                        return
                            $analysisGroup ?
                                $this->getLink($analysisGroup->getName(), $analysisGroup->getId(), 'analysis_group_show')
                                : '';
                    }
                ]
            )
            ->add(
                'timeRange', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('timeRange'),
                    'render' => function (string $data, PlanTesting $planTesting) {
                        /** @var TimeRange $timeRange */
                        $timeRange = $planTesting->getTimeRange();
                        return
                            $timeRange ?
                                $this->getLink($timeRange->getTitle(), $timeRange->getId(), 'time_range_show')
                                : '';
                    }
                ]
            )
            ->add(
                'timeRangeCount', NumberColumn::class, [
                    'label' => $listTemplateItem->getContentValue('timeRangeCount'),
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PlanTesting::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('pt')
                            ->from(PlanTesting::class, 'pt');
                    },
                ]
            );
    }
}