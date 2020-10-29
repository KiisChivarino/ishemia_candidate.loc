<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\TemplateType;
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
class TemplateTypeDataTableService extends AdminDatatableService
{
    /**
     * Таблица типов шаблонов в админке
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
           ;
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => TemplateType::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('t')
                            ->from(TemplateType::class, 't');
                    },
                ]
            );
    }
}