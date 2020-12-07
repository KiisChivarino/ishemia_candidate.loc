<?php

namespace App\Services\DataTable\Admin;

use App\Entity\StartingPoint;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class StartingPointDataTableService
 * @package App\Services\DataTable\Admin
 */
class StartingPointDataTableService extends AdminDatatableService
{
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
                'title', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('title'),
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => StartingPoint::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('sp')
                            ->from(StartingPoint::class, 'sp');
                    },
                ]
            );
    }
}