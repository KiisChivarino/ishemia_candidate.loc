<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\City;
use App\Entity\Hospital;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class HospitalDataTableService
 * Таблица больниц для кабинета врача
 *
 * @package App\Services\DataTable\DoctorOffice
 */
class HospitalDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array|null $filters
     * @param array|null $options
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        ?array $filters,
        ?array $options
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name'),
                    'render' => function (string $data, Hospital $hospital) use ($options) {
                        return '<a href="' . $this->router->generate(
                                'patients_list',
                                [
                                    $options['filterService']
                                        ->generateFilterName(
                                            'patients_list',
                                            Hospital::class
                                        ) => $hospital->getId()
                                ]
                            ) . '">' . $hospital->getName() . '</a>';
                    },
                    'field' => 'h.name',
                    'orderable' => true,
                    'orderField' => 'h.name'
                ]
            )
            ->add(
                'city', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('city'),
                    'render' => function (string $data, Hospital $hospital) {
                        /** @var City $city */
                        $city = $hospital->getCity();
                        return $city ? $city->getName() : '';
                    },
                    'field' => 'c.name',
                    'orderable' => true,
                    'orderField' => 'c.name'
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Hospital::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('h')
                            ->from(Hospital::class, 'h')
                            ->andWhere('h.enabled = true')
                            ->leftJoin('h.city', 'c');
                    },
                ]
            );
    }
}