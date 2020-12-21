<?php

namespace App\Services\DataTable\Admin;

use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class HospitalDataTableService
 * таблица больниц
 *
 * @package App\Services\DataTable\Admin
 */
class HospitalDataTableService extends AdminDatatableService
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
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name'),
                ]
            )
            ->add(
                'region', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('region'),
                    'render' => function (string $data, Hospital $hospital) {
                        /** @var Region $region */
                        $region = $hospital->getRegion();
                        return $region ? $this->getLink($region->getName(), $region->getId(), 'region_show') : '';
                    },
                    'searchable' => false
                ]
            )
            ->add(
                'city', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('city'),
                    'render' => function (string $data, Hospital $hospital) {
                        /** @var City $city */
                        $city = $hospital->getCity();
                        return $city ? $this->getLink($city->getName(), $city->getId(), 'city_show') : '';
                    },
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Hospital::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('h')
                            ->from(Hospital::class, 'h');
                    },
                ]
            );
    }
}