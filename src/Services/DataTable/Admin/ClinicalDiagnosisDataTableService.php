<?php

namespace App\Services\DataTable\Admin;

use App\Entity\ClinicalDiagnosis;
use App\Entity\Diagnosis;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class ClinicalDiagnosisDataTableService
 * @package App\Services\DataTable\Admin
 */
class ClinicalDiagnosisDataTableService extends AdminDatatableService
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
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                    'raw' => true,
                ]
            )
            ->add(
                'MKBCode', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('MKBCode'),
                    'render' => function (string $data, ClinicalDiagnosis $clinicalDiagnosis) {
                        /** @var Diagnosis $diagnosis */
                        $diagnosis = $clinicalDiagnosis->getMKBCode();
                        return
                            $diagnosis ? $this->getLink(
                                $diagnosis->getCode(),
                                $diagnosis->getId(),
                                'diagnosis_show'
                            ) : '';
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => ClinicalDiagnosis::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('cd')
                            ->from(ClinicalDiagnosis::class, 'cd');
                    },
                ]
            );
    }
}