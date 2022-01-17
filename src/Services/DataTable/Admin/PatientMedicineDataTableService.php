<?php

namespace App\Services\DataTable\Admin;

use App\Entity\PatientMedicine;
use App\Entity\Prescription;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PatientMedicineDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PatientMedicineDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'patient', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patient'),
                    'render' => function (string $data, PatientMedicine $patientMedicine) use ($listTemplateItem) {
                        $content = $listTemplateItem->getContentValue('empty');
                        if ($prescriptionMedicine = $patientMedicine->getPrescriptionMedicine()) {
                            if ($prescription = $prescriptionMedicine->getPrescription()) {
                                if ($medicalHistory = $prescription->getMedicalHistory()) {
                                    if ($patient = $medicalHistory->getPatient()) {
                                        $content = $this->getLink(
                                            AuthUserInfoService::getFIO($patient->getAuthUser(), true),
                                            $medicalHistory->getId(),
                                            'medical_history_show'
                                        );
                                    }
                                }
                            }
                        }
                        return $content;
                    }
                ]
            )
            ->add(
                'prescriptionMedicine', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('prescriptionMedicine'),
                    'render' => function (string $data, PatientMedicine $patientMedicine) use ($listTemplateItem) {
                        $prescriptionMedicine = $patientMedicine->getPrescriptionMedicine();
                        return $prescriptionMedicine ?
                            $this->getLinkMultiParam(
                                'Назначение лекарств от ' . $prescriptionMedicine->getInclusionTime()->format('d.m.Y'),
                                [
                                    'prescriptionMedicine' => $prescriptionMedicine->getId(),
                                    'prescription' => $prescriptionMedicine->getPrescription()->getId()
                                ],
                                'prescription_medicine_show'
                            ) : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'medicineName', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicineName'),
                ]
            )
            ->add(
                'instruction', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('instruction'),
                    'render' => function (string $data, PatientMedicine $patientMedicine) use ($listTemplateItem) {
                        return $patientMedicine->getInstruction() ?? $listTemplateItem->getContentValue('empty');
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Prescription $prescription */
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientMedicine::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('pm')
                            ->from(PatientMedicine::class, 'pm');
                    },
                ]
            );
    }
}