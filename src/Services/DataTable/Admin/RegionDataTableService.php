<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Country;
use App\Entity\Region;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class RegionDataTableService
 * table for regions
 *
 * @package App\Services\DataTable
 */
class RegionDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     */
    public function getTable(
        Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem
    ): DataTable {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'country', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('country'),
                    'render' => function (string $data, Region $region) {
                        /** @var Country $country */
                        $country = $region->getCountry();
                        return
                            $country ? $this->getLink($country->getName(), $country->getId(), 'country_show') : '';
                    },
                ]
            )
            ->add(
                'name', TextColumn::class, ['label' => $listTemplateItem->getContentValue('name')]
            )
            ->add(
                'region_number', TextColumn::class, ['label' => $listTemplateItem->getContentValue('region_number')]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Region::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('r')
                            ->from(Region::class, 'r');
                    },
                ]
            );
    }
}