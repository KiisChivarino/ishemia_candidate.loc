<?php

namespace App\Services\DataTable\Admin;

use App\Entity\TimeRange;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class TimeRangeDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class TimeRangeDataTableService extends AdminDatatableService
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
                'title', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('rangeTitle'),
                ]
            )
            ->add(
                'dateInterval', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateInterval'),
                    'field' => 'di.title',
                    'searchable' => true
                ]
            )
            ->add(
                'multiplier', NumberColumn::class, [
                    'label' => $listTemplateItem->getContentValue('multiplier'),
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => TimeRange::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('c')
                            ->from(TimeRange::class, 'c')
                            ->leftJoin('c.dateInterval', 'di');
                    },
                ]
            );
    }
}