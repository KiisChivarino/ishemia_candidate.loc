<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Measure;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class MeasureDataTableService
 * таблица единиц измерения
 *
 * @package App\Services\DataTable\Admin
 */
class MeasureDataTableService extends AdminDatatableService
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
                'nameRu', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name'),
                ]
            )
            ->add(
                'nameEn', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('nameEn'),
                ]
            )
            ->add(
                'title', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('measureTitle'),
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Measure::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('m')
                            ->from(Measure::class, 'm');
                    },
                ]
            );
    }
}