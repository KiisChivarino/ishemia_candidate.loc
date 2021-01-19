<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\Region;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\FilterService\FilterService;
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
     * @param string $routeName
     * @param string $entityClassName
     * @return string
     */
    public function generateFilterName(string $routeName, string $entityClassName): string
    {
        return
            'filter_'
            .str_replace('_', '', $routeName).'_'
            .mb_strtolower(substr($entityClassName, strripos($entityClassName, '\\') + 1));
    }

    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
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
                    'render' => function (string $data, Hospital $hospital) {
                    return '<a href="'.$this->router->generate('patients_list', [$this->generateFilterName('patients_list', Hospital::class) => $hospital->getId()]).'">'.$hospital->getName().'</a>';
                    },
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
            ;
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