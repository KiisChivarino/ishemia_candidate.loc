<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Hospital;
use App\Entity\Position;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableState;

/**
 * Class StaffDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class StaffDataTableService extends AdminDatatableService
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
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'data' => function ($value) {
                        return (new AuthUserInfoService())->getFIO($value->getAuthUser());
                    }
                ]
            )
            ->add(
                'lastName', TextColumn::class, [
                    'visible' => false,
                    'field' => 'upper(aU.lastName)',
                ]
            )
            ->add(
                'firstName', TextColumn::class, [
                    'visible' => false,
                    'field' => 'upper(aU.firstName)',
                ]
            )
            ->add(
                'patronymicName', TextColumn::class, [
                    'visible' => false,
                    'field' => 'upper(aU.patronymicName)',
                ]
            )
            ->add(
                'hospital', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('hospital'),
                    'render' => function (string $data, Staff $staff) use ($listTemplateItem) {
                        /** @var Hospital $hospital */
                        $hospital = $staff->getHospital();
                        return
                            $hospital ?
                                $this->getLink($hospital->getName(), $hospital->getId(), 'hospital_show')
                                : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'position', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('position'),
                    'searchable' => false,
                    'orderable' => false,
                    'render' => function (string $data, Staff $staff) {
                        /** @var Position $position */
                        $position = $staff->getPosition();
                        return
                            $position ?
                                $this->getLink($position->getName(), $position->getId(), 'position_show')
                                : '';
                    },
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Staff::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('s')
                            ->from(Staff::class, 's')
                            ->leftJoin('s.AuthUser', 'aU');
                    },
                    'criteria' => [
                        function (QueryBuilder $queryBuilder, DataTableState $state) {
                            $state->setGlobalSearch(mb_strtoupper($state->getGlobalSearch()));
                        },
                        new SearchCriteriaProvider(),
                    ],
                ]
            );
    }
}