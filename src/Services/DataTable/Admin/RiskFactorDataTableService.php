<?php

namespace App\Services\DataTable\Admin;

use App\Entity\RiskFactor;
use App\Entity\RiskFactorType;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class RiskFactorDataTableService
 * table of risk factors
 *
 * @package App\Services\DataTable\Admin
 */
class RiskFactorDataTableService extends AdminDatatableService
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
                'riskFactorType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('riskFactorType'),
                    'render' => function (string $data, RiskFactor $riskFactor) {
                        /** @var RiskFactorType $riskFactorType */
                        $riskFactorType = $riskFactor->getRiskFactorType();
                        return
                            $riskFactorType ?
                                $this->getLink(
                                    $riskFactorType->getName(),
                                    $riskFactorType->getId(),
                                    'risk_factor_type_show'
                                ) : '';
                    },
                ]
            )
            ->add(
                'name', TextColumn::class, ['label' => $listTemplateItem->getContentValue('name')]
            )
            ->add(
                'scores', TextColumn::class, ['label' => $listTemplateItem->getContentValue('scores')]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => RiskFactor::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('rf')
                            ->from(RiskFactor::class, 'rf');
                    },
                ]
            );
    }
}