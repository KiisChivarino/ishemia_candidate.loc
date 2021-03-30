<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\AuthUser;
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
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'render' => function (string $data, PatientTesting $patientTesting) {
                        /** @var AuthUser $authUser */
                        $authUser = $patientTesting->getMedicalHistory()->getPatient()->getAuthUser();
                        return
                            $authUser ? $this->getLink(
                                (new AuthUserInfoService())->getFIO($authUser, true),
                                $patientTesting->getMedicalHistory()->getPatient()->getId(),
                                'patient_show'
                            ) : '';
                    }
                ]
            )
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, PatientTesting $patientTesting) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $patientTesting->getAnalysisGroup();
                        return
                            $analysisGroup ?
                                $this->getLink($analysisGroup->getName(), $analysisGroup->getId(), 'analysis_group_show')
                                : '';
                    }
                ]
            )
            ->add(
                'analysisDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisDate'),
                    'format' => 'd.m.Y',
                    'nullValue' => 'нет',
                    'searchable' => false,
                ]
            )
            ->add(
                'isProcessedByStaff', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('processed'),
                    'trueValue' => 'да',
                    'falseValue' => 'нет',
                    'searchable' => false,
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Patient $patient */
        $patient = isset($filters[AppAbstractController::FILTER_LABELS['PATIENT']]) ? $filters[AppAbstractController::FILTER_LABELS['PATIENT']] : null;
        /** @var MedicalHistory $patient */
        $medicalHistory = isset($filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']]) ? $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']] : null;
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