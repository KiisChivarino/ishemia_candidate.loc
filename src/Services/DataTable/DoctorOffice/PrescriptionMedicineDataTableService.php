<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ShowTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionMedicineDataTableService
 * @package App\Services\DataTable\DoctorOffice
 */
class PrescriptionMedicineDataTableService extends DoctorOfficeDatatableService
{
    /** @var string class of main entity */
    public const ENTITY_CLASS = PrescriptionMedicine::class;

    public const DATATABLE_NAME = 'PrescriptionMedicineDataTable';

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
            ->setName(self::DATATABLE_NAME);
        $this->dataTable
            ->add(
                'startingMedicationDate', DateTimeColumn::class, [
                    'label' => $showTemplateItem->getContentValue('startingMedicationDate'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            )
            ->add(
                'endMedicationDate', DateTimeColumn::class, [
                    'label' => $showTemplateItem->getContentValue('endMedicationDate'),
                    'format' => 'd.m.Y',
                    'searchable' => false,
                    'render' => function (string $data) use ($showTemplateItem) {
                        return
                            $data
                                ? htmlspecialchars_decode($data)
                                : $showTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'medicine_name', TextColumn::class, [
                    'label' => $showTemplateItem->getContentValue('medicine_name'),
                    'field' => 'pma.medicine_name',
                    'searchable' => true,
                    'render' => function (string $data) use ($showTemplateItem) {
                        return
                            $data ?: $showTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'instruction', TextColumn::class, [
                    'label' => $showTemplateItem->getContentValue('instruction'),
                    'field' => 'pma.instruction',
                    'searchable' => true,
                    'render' => function (string $data) use ($showTemplateItem) {
                        return
                            $data
                                ? htmlspecialchars_decode($data)
                                : $showTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                'label' => $showTemplateItem->getContentValue('doctor'),
                'render' => function (string $data, PrescriptionMedicine $prescriptionMedicine) use ($showTemplateItem) {
                    $staff = $prescriptionMedicine->getStaff();
                    return AuthUserInfoService::getFIO($staff->getAuthUser());
                },
            ]);

        $this->addOperations($renderOperationsFunction, $showTemplateItem);

        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => self::ENTITY_CLASS,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pm')
                            ->from(self::ENTITY_CLASS, 'pm')
                            ->join('pm.patientMedicine', 'pma');
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