<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PatientTestingDataTableService
 * таблица тестирования пациента
 *
 * @package App\Services\DataTable\Admin
 */
class PatientTestingDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'render' => function (string $data, PatientTesting $patientTesting) use ($listTemplateItem) {
                        $patient = $patientTesting->getMedicalHistory()->getPatient();
                        return
                            $this->getLinkMultiParam(
                                AuthUserInfoService::getFIO($patient->getAuthUser(), true),
                                [
                                    'patient' => $patient->getId(),
                                ],
                                'patient_show'
                            );
                    }
                ]
            )
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, PatientTesting $patientTesting) use ($listTemplateItem) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $patientTesting->getAnalysisGroup();
                        $patientTesting->getPatientTestingResults(); //черная магия datatables - без этого падает 502
                        return
                            $this->getLink(
                                $analysisGroup->getName(),
                                $analysisGroup->getId(),
                                'analysis_group_show'
                            );
                    }
                ]
            )
            ->add(
                'analysisDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisDate'),
                    'format' => 'd.m.Y',
                    'nullValue' => $listTemplateItem->getContentValue('falseValue'),
                    'searchable' => false,
                ]
            )
            ->add(
                'isProcessedByStaff', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isProcessedByStaff'),
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'searchable' => false,
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Patient $patient */
        $patient = $filters[AppAbstractController::FILTER_LABELS['PATIENT']] ?? null;
        /** @var MedicalHistory $patient */
        $medicalHistory = $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']] ?? null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientTesting::class,
                    'query' => function (QueryBuilder $builder) use ($patient, $medicalHistory) {
                        $builder
                            ->select('pt')
                            ->from(PatientTesting::class, 'pt');
                        if ($patient) {
                            $builder
                                ->leftJoin('pt.medicalHistory', 'mh')
                                ->andWhere('mh.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                        if ($medicalHistory) {
                            $builder
                                ->andWhere('pt.medicalHistory = :medicalHistory')
                                ->setParameter('medicalHistory', $medicalHistory);
                        }
                    },
                ]
            );
    }
}