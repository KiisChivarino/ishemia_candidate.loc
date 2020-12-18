<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
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
class LogDataTableService extends AdminDatatableService
{
    /**
     * Таблица логов
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'logAction', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('logAction'),
                    'orderable' => false,
                    'render' => function (string $data, Log $log) {
                        /** @var AnalysisGroup $analysisGroup */
                        $logAction= $log->getAction();
                        return $logAction->getName();
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
                    'label' => $listTemplateItem->getContentValue('createdAt'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:i',
                ]
            )
            ;

        /** @var LogAction $logAction */
        $logAction = isset($filters[AppAbstractController::FILTER_LABELS['LOG_ACTION']])
            ? $filters[AppAbstractController::FILTER_LABELS['LOG_ACTION']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Log::class,
                    'query' => function (QueryBuilder $builder) use ($logAction) {
                        $builder
                            ->select('l')
                            ->from(Log::class, 'l')
                            ->orderBy('l.id', 'desc')
                        ;
                        if ($logAction) {
                            $builder
                                ->andWhere('l.action = :action')
                                ->setParameter('action', $logAction);
                        }
                    },
                ]
            );
    }
}