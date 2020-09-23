<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Role;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class RoleDataTableService
 * table of roles
 *
 * @package App\Services\DataTable\Admin
 */
class RoleDataTableService extends AdminDatatableService
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
                'name', TextColumn::class, ['label' => $listTemplateItem->getContentValue('name')]
            )
            ->add(
                'tech_name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('tech_name'),
                    'data' => function ($value) {
                        return str_replace('ROLE_', '', $value->getTechName());
                    }
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Role::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('r')
                            ->from(Role::class, 'r');
                    },
                ]
            );
    }
}