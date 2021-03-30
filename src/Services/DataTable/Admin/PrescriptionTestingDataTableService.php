<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionTestingDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PrescriptionTestingDataTableService extends AdminDatatableService
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
                'prescription', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('prescription'),
                    'render' => function (string $data, PrescriptionTesting $prescriptionTesting) {
                        $prescription = $prescriptionTesting->getPrescription();
                        return $prescription ? $this->getLink(
                            PrescriptionInfoService::getPrescriptionTitle($prescription),
                            $prescription->getId(),
                            'prescription_show'
                        ) : 'отсутствует';
                    }
                ]
            )
            ->add(
                'patientTesting', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patientTesting'),
                    'render' => function (string $data, PrescriptionTesting $prescriptionTesting) {
                        /** @var PatientTesting $patientTesting */
                        $patientTesting = $prescriptionTesting->getPatientTesting();
                        return $patientTesting ? $this->getLink(
                            PatientTestingInfoService::getPatientTestingInfoString($patientTesting),
                            $patientTesting->getId(),
                            'patient_testing_show'
                        ) : 'отсутствует';
                    },
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, PrescriptionTesting $prescriptionTesting) {
                        /** @var Staff $staff */
                        $staff = $prescriptionTesting->getStaff();
                        return $staff ? $this->getLink(
                            AuthUserInfoService::getFIO($staff->getAuthUser()),
                            $staff->getId(),
                            'staff_show'
                        ) : 'отсутствует';
                    }
                ]
            )
            ->add(
                'plannedDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('plannedDate'),
                    'format' => 'd.m.Y H:m',
                    'searchable' => false,
                    'nullValue' => 'нет'
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Prescription $prescription */
        $prescription = isset($filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']]) ? $filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PrescriptionTesting::class,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pt')
                            ->from(PrescriptionTesting::class, 'pt');
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