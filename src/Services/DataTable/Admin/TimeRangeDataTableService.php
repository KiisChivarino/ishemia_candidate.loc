<?php

namespace App\Services\DataTable\Admin;

use App\Entity\TimeRange;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
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
     * @throws Exception
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
            )
            ->add('isRegular', BoolColumn::class, [
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'label' => $listTemplateItem->getContentValue('isRegular'),
                    'searchable' => false,
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