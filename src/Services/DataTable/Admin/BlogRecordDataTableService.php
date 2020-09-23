<?php

namespace App\Services\DataTable\Admin;

use App\Entity\BlogRecord;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class BlogRecordDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class BlogRecordDataTableService extends AdminDatatableService
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
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => BlogRecord::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('br')
                            ->from(BlogRecord::class, 'br');
                    },
                ]
            );
    }
}