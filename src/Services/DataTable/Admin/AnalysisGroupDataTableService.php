<?php

namespace App\Services\DataTable\Admin;

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
class AnalysisGroupDataTableService extends AdminDatatableService
{
    /**
     * Таблица группы анализов в админке
     *
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
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name')
                ]
            )
            ->add(
                'fullName', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fullName'),
                    'render' => function (string $data, AnalysisGroup $analysisGroup) use ($listTemplateItem) {
                        return
                            $analysisGroup->getFullName() ? $analysisGroup->getFullName()
                                : $listTemplateItem->getContentValue('empty');
                    }
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