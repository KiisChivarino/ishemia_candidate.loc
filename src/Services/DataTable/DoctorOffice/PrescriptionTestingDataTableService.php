<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\AnalysisGroup;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\TemplateItems\ShowTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionTestingDataTableService
 * @package App\Services\DataTable\DoctorOffice
 */
class PrescriptionTestingDataTableService extends AdminDatatableService
{

    /**
     * @param Closure $renderOperationsFunction
     * @param ShowTemplateItem $showTemplateItem
     * @param Prescription $prescription
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ShowTemplateItem $showTemplateItem,
        Prescription $prescription

    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'analysisGroup', TextColumn::class, [
                    'label' => $showTemplateItem->getContentValue('analysisGroup'),
                    'render' => function (string $data, PrescriptionTesting $prescriptionTesting) {
                        /** @var AnalysisGroup $analysisGroup */
                        $analysisGroup = $prescriptionTesting->getPatientTesting()->getAnalysisGroup();
                        return $analysisGroup->getName();
                    },
                ]
            )
            ->add(
                'plannedDate', DateTimeColumn::class, [
                    'label' => $showTemplateItem->getContentValue('plannedDate'),
                    'format' => 'd.m.Y H:m',
                    'searchable' => false
                ]
            );
//        $this->addEnabled($showTemplateItem);
        $this->addOperations($renderOperationsFunction, $showTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PrescriptionTesting::class,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pt')
                            ->from(PrescriptionTesting::class, 'pt')
                            ->join('pt.patientTesting', 'ptt')
                        ;
                        if ($prescription) {
                            $builder
                                ->andWhere('pt.prescription = :prescription')
                                ->setParameter('prescription', $prescription);
                        }
                    },
                ]
            );
    }
}