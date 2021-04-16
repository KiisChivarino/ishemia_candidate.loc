<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
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
 * Class PrescriptionMedicineDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PrescriptionMedicineDataTableService extends AdminDatatableService
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
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) use ($listTemplateItem) {
                        $prescription = $prescriptionMedicine->getPrescription();
                        return $prescription ? $this->getLink(
                            (new PrescriptionInfoService())->getPrescriptionTitle($prescription),
                            $prescription->getId(),
                            'prescription_show'
                        ) : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) use ($listTemplateItem) {
                        /** @var Staff $staff */
                        $staff = $prescriptionMedicine->getStaff();
                        return $staff ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($staff->getAuthUser()),
                            $staff->getId(),
                            'staff_show'
                        ) : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'startingMedicationDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('startingMedicationDate'),
                    'searchable' => false,
                    'format' => 'd.m.Y',

                ]
            )
            ->add(
                'endMedicationDate', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('endMedicationDate'),
                    'searchable' => false,
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) use ($listTemplateItem) {
                        return $prescriptionMedicine->getEndMedicationDate()
                            ? $prescriptionMedicine->getEndMedicationDate()->format('d.m.Y')
                            : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
        ;
        $this->addEnabled($listTemplateItem);
        $this->addOperationsWithParameters(
            $listTemplateItem,
            function (int $patientTestingId, PrescriptionMedicine $prescriptionMedicine) use ($renderOperationsFunction) {
                return
                    $renderOperationsFunction(
                        (string)$prescriptionMedicine->getId(),
                        $prescriptionMedicine,
                        'prescription_medicine_show',
                        [
                            'prescriptionMedicine' => $patientTestingId,
                            'prescription' => $prescriptionMedicine->getPrescription()->getId()
                        ]
                    );
            }
        );
        /** @var Prescription $prescription */
        $prescription = $filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']] ?? null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PrescriptionMedicine::class,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pm')
                            ->from(PrescriptionMedicine::class, 'pm');
                        if ($prescription) {
                            $builder
                                ->andWhere('pm.prescription = :prescription')
                                ->setParameter('prescription', $prescription);
                        }
                    },
                ]
            );
    }
}