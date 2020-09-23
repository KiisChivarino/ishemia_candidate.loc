<?php

namespace App\Services\DataTable\Admin;

use App\Entity\RiskFactorType;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class RiskFactorTypeDataTableService
 * table of risk factor types
 *
 * @package App\Services\DataTable\Admin
 */
class RiskFactorTypeDataTableService extends AdminDatatableService
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
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => RiskFactorType::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('rft')
                            ->from(RiskFactorType::class, 'rft');
                    },
                ]
            );
    }
}