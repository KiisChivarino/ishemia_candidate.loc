<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class AnalysisGroupDataTableService extends AdminDatatableService
{
    /**
     * Таблица группы анализов в админке
     *
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
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name')
                ]
            )
            ->add(
                'fullName', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fullName'),
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => AnalysisGroup::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('g')
                            ->from(AnalysisGroup::class, 'g');
                    },
                ]
            );
    }
}