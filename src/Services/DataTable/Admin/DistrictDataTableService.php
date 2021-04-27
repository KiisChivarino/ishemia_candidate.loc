<?php

namespace App\Services\DataTable\Admin;

use App\Entity\District;
use App\Entity\Region;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DistrictDataTableService
 * Сервис для вывода таблицы районов в админке
 *
 * @package App\Services\DataTable\Admin
 */
class DistrictDataTableService extends AdminDatatableService
{
    /**
     * Таблица районов в админке
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
                    'label' => $listTemplateItem->getContentValue('name'),
                ]
            )
            ->add(
                'region', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('region'),
                    'render' => function (string $data, District $district) {
                        /** @var Region $region */
                        $region = $district->getRegion();
                        return $region ? $this->getLink($region->getName(), $region->getId(), 'region_show') : '';
                    },
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => District::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('d')
                            ->from(District::class, 'd');
                    },
                ]
            );
    }
}