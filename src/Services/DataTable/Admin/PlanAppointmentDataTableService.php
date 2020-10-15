<?php

namespace App\Services\DataTable\Admin;

use App\Entity\PlanAppointment;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PlanAppointmentDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PlanAppointmentDataTableService extends AdminDatatableService
{
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'dateBegin', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateBegin'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            )
            ->add(
                'dateEnd', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateEnd'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PlanAppointment::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('pa')
                            ->from(PlanAppointment::class, 'pa');
                    },
                ]
            );
    }
}