<?php

namespace App\Services\DataTable\Admin;

use App\Entity\PlanAppointment;
use App\Entity\TimeRange;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PlanAppointmentDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PlanAppointmentDataTableService extends AdminDatatableService
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
                'timeRange', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('timeRange'),
                    'render' => function (string $data, PlanAppointment $planAppointment) {
                        /** @var TimeRange $timeRange */
                        $timeRange = $planAppointment->getTimeRange();
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
                    'entity' => PlanAppointment::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('pa')
                            ->from(PlanAppointment::class, 'pa');
                    },
                ]
            );
    }
}