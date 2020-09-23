<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Medicine;
use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
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
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'prescription', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('prescription'),
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) {
                        $prescription = $prescriptionMedicine->getPrescription();
                        return $prescription ? $this->getLink(
                            (new PrescriptionInfoService())->getPrescriptionTitle($prescription),
                            $prescription->getId(),
                            'prescription_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'medicine', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicine'),
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) {
                        /** @var Medicine $medicine */
                        $medicine = $prescriptionMedicine->getMedicine();
                        return
                            $medicine ?
                                $this->getLink($medicine->getName(), $medicine->getId(), 'medicine_show')
                                : '';
                    }
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) {
                        /** @var Staff $staff */
                        $staff = $prescriptionMedicine->getStaff();
                        return $staff ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($staff->getAuthUser()),
                            $staff->getId(),
                            'staff_show'
                        ) : '';
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Prescription $prescription */
        $prescription = isset($filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']]) ? $filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']] : null;
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