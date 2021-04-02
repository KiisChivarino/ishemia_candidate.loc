<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Analysis;
use App\Entity\AnalysisRate;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PatientTestingResultDataTableService
 * table for list patient testing results
 *
 * @package App\Services\DataTable\Admin
 */
class PatientTestingResultDataTableService extends AdminDatatableService
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
        array $filters = []
    ): DataTable {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'patientTesting', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patientTesting'),
                    'render' => function (string $data, PatientTestingResult $patientTestingResult) use ($listTemplateItem) {
                        /** @var PatientTesting $patientTesting */
                        $patientTesting = $patientTestingResult->getPatientTesting();
                        return $patientTesting ? $this->getLink(
                            (new PatientTestingInfoService())->getPatientTestingInfoString($patientTesting),
                            $patientTesting->getId(),
                            'patient_testing_show'
                        ) : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'analysis', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysis'),
                    'render' => function (string $data, PatientTestingResult $patientTestingResult) use ($listTemplateItem) {
                        /** @var Analysis $analysis */
                        $analysis = $patientTestingResult->getAnalysis();
                        return
                            $analysis ?
                                $this->getLink($analysis->getName(), $analysis->getId(), 'analysis_show')
                                : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'analysisRate', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('analysisRate'),
                    'render' => function (string $data, PatientTestingResult $patientTestingResult) use ($listTemplateItem) {
                        /** @var AnalysisRate $analysisRate */
                        $analysisRate = $patientTestingResult->getAnalysisRate();
                        return $analysisRate ? $this->getLink(
                            (new AnalysisRateInfoService())->getAnalysisRateInfoString($analysisRate),
                            $analysisRate->getId(),
                            'analysis_rate_show'
                        ) : $listTemplateItem->getContentValue('empty_plural');
                    },
                ]
            )
            ->add('result',TextColumn::class,
                [
                    'label' => $listTemplateItem->getContentValue('result'),
                    'render' => function (string $data, PatientTestingResult $patientTestingResult) use ($listTemplateItem) {
                        return $patientTestingResult->getResult() ? $patientTestingResult->getResult() : $listTemplateItem->getContentValue('empty');
                    },
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Patient $patient */
        $patient = $filters['patient'];
        /** @var PatientTesting $patientTesting */
        $patientTesting = $filters['patientTesting'];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientTestingResult::class,
                    'query' => function (QueryBuilder $builder) use ($patient, $patientTesting) {
                        $builder
                            ->select('ptr')
                            ->from(PatientTestingResult::class, 'ptr')
                            ->leftJoin('ptr.patientTesting', 'pt');
                        if ($patient) {
                            $builder
                                ->leftJoin('pt.medicalHistory', 'mh')
                                ->andWhere('mh.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                        if ($patientTesting) {
                            $builder
                                ->andWhere('ptr.patientTesting = :patientTesting')
                                ->setParameter('patientTesting', $patientTesting);
                        }
                    },
                ]
            );
    }
}