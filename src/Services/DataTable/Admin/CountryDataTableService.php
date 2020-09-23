<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Country;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class CountryDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class CountryDataTableService extends AdminDatatableService
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
                    'label' => $listTemplateItem->getContentValue('name'),
                ]
            )
            ->add(
                'shortcode', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('shortcode'),
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Country::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('c')
                            ->from(Country::class, 'c');
                    },
                ]
            );
    }
}