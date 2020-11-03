<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\Logger\Log;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
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
class LogDataTableService extends AdminDatatableService
{
    /**
     * Таблица логов
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @return DataTable
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'logAction', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('logAction'),
                    'render' => function (string $data, Log $log) {
                        /** @var AnalysisGroup $analysisGroup */
                        $logAction= $log->getAction();
                        return $logAction ? $this->getLink($logAction->getName(), $logAction->getId(), 'log_action_show') : '';
                    }
                ]
            )
            ->add(
                'description', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('description'),
                ]
            )
            ->add(
                'userString', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('userString')
                ]
            )
            ->add(
                'created_at', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('createdAt')
                ]
            )
            ;

        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Log::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('l')
                            ->from(Log::class, 'l')
                            ->orderBy('l.id', 'desc')
                        ;
                    },
                ]
            );
    }
}