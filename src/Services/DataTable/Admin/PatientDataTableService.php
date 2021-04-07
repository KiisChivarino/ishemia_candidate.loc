<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientDataTableService extends AdminDatatableService
{
    /**
     * Таблица диагнозов в админке
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
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'data' => function ($value) {
                        return (new AuthUserInfoService())->getFIO($value->getAuthUser());
                    }
                ]
            )
            ->add(
                'insuranceNumber', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('insuranceNumber'),
                    'render' => function ($value) use ($listTemplateItem) {
                        return $value ? $value : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'dateBirth', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateBirth'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            )
            ->add(
                'phone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('phone'),
                    'data' => function ($value) {
                        return $value->getAuthUser()->getPhone();
                    }
                ]
            );
        $this->addEnabled($listTemplateItem, 'a');
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Patient::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('p')
                            ->from(Patient::class, 'p')
                            ->leftJoin('p.AuthUser', 'a');
                    },
                ]
            );
    }
}