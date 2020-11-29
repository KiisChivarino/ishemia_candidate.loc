<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientAppointmentInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionAppointmentDataTableService
 * @package App\Services\DataTable\Admin
 */
class PrescriptionAppointmentDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
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
                    'render' => function (string $data, PrescriptionAppointment $prescriptionAppointment) {
                        $prescription = $prescriptionAppointment->getPrescription();
                        return $prescription ? $this->getLink(
                            (new PrescriptionInfoService())->getPrescriptionTitle($prescription),
                            $prescription->getId(),
                            'prescription_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'patientAppointment', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patientAppointment'),
                    'render' => function (string $data, PrescriptionAppointment $prescriptionAppointment) {
                        /** @var PatientAppointment $patientAppointment */
                        $patientAppointment = $prescriptionAppointment->getPatientAppointment();
                        return $patientAppointment ? $this->getLink(
                            PatientAppointmentInfoService::getPatientAppointmentInfoString($patientAppointment),
                            $patientAppointment->getId(),
                            'patient_appointment_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, PrescriptionAppointment $prescriptionAppointment) {
                        /** @var Staff $staff */
                        $staff = $prescriptionAppointment->getStaff();
                        return $staff ? $this->getLink(
                            AuthUserInfoService::getFIO($staff->getAuthUser()),
                            $staff->getId(),
                            'staff_show'
                        ) : '';
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Prescription $prescription */
        $prescription =
            isset($filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']])
                ? $filters[AppAbstractController::FILTER_LABELS['PRESCRIPTION']]
                : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PrescriptionAppointment::class,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pa')
                            ->from(PrescriptionAppointment::class, 'pa');
                        if ($prescription) {
                            $builder
                                ->andWhere('pa.prescription = :prescription')
                                ->setParameter('prescription', $prescription);
                        }
                    },
                ]
            );
    }
}