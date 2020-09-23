<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\PlanTesting;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PlanTestingDataTableService
 * the table of list plan testing
 *
 * @package App\Services\DataTable\Admin
 */
class PlanTestingDataTableService extends AdminDatatableService
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
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, PlanTesting $planTesting) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $planTesting->getAnalysisGroup();
                        return
                            $analysisGroup ?
                                $this->getLink($analysisGroup->getName(), $analysisGroup->getId(), 'analysis_group_show')
                                : '';
                    }
                ]
            )
//            ->add(
//                'minMaxAge', TextColumn::class, [
//                    'label' => $listTemplateItem->getContentValue('minMaxAge'),
//                    'data' => function ($value) {
//                        return $value->getGestationalMinAge().'-'.$value->getGestationalMaxAge();
//                    }
//                ]
//            )
        ;
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PlanTesting::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('pt')
                            ->from(PlanTesting::class, 'pt');
                    },
                ]
            );
    }
}