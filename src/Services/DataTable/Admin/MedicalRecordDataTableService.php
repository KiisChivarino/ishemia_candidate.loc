<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class MedicalRecordDataTableService
 * table list of medical history records
 *
 * @package App\Services\DataTable\Admin
 */
class MedicalRecordDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'medicalHistory', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalHistory'),
                    'render' => function ($dataString, $medicalRecord) {
                        /** @var MedicalHistory $medicalHistory */
                        $medicalHistory = $medicalRecord->getMedicalHistory();
                        return $medicalHistory ? $this->getLink(
                            (new MedicalHistoryInfoService())->getMedicalHistoryTitle($medicalHistory),
                            $medicalHistory->getId(),
                            'medical_history_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'recordDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('recordDate'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => MedicalRecord::class,
                    'query' => function (QueryBuilder $builder) use ($medicalHistory) {
                        $builder
                            ->select('mr')
                            ->from(MedicalRecord::class, 'mr');
                        if ($medicalHistory) {
                            $builder
                                ->andWhere('mr.medicalHistory = :medicalHistory')
                                ->setParameter('medicalHistory', $medicalHistory);
                        }
                    },
                ]
            );
    }
}