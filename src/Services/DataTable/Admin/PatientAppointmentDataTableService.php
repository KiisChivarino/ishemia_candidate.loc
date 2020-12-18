<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AppointmentType;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
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
 * Class PatientAppointmentDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PatientAppointmentDataTableService extends AdminDatatableService
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
                'medicalRecord', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalRecord'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment): string {
                        /** @var MedicalRecord $medicalRecord */
                        $medicalRecord = $patientAppointment->getMedicalRecord();
                        return $medicalRecord ? $this->getLink(
                            (new MedicalRecordInfoService())->getMedicalRecordTitle($medicalRecord),
                            $medicalRecord->getId(),
                            'medical_record_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment): string {
                        /** @var Staff $staff */
                        $staff = $patientAppointment->getStaff();
                        return $staff ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true),
                            $staff->getId(),
                            'staff_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'appointmentType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('appointmentType'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment): string {
                        /** @var AppointmentType $appointmentType */
                        $appointmentType = $patientAppointment->getAppointmentType();
                        return $appointmentType ? $this->getLink(
                            $appointmentType->getName(),
                            $appointmentType->getId(),
                            'appointment_type_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'plannedDateTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('plannedDateTime'),
                    'field' => 'pra.plannedDateTime',
                    'searchable' => false,
                    'format' => 'd.m.Y H:i'
                ]
            )
            ->add(
                'appointmentTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('appointmentTime'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:i'
                ]
            )
            ->add(
                'isConfirmed', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isConfirmed'),
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'searchable' => false,
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = isset($filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']]) ? $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientAppointment::class,
                    'query' => function (QueryBuilder $builder) use ($medicalHistory) {
                        $builder
                            ->select('pa')
                            ->from(PatientAppointment::class, 'pa')
                            ->innerJoin('pa.prescriptionAppointment', 'pra')
                        ;
                        if ($medicalHistory) {
                            $builder
                                ->andWhere('pa.medicalHistory = :medicalHistory')
                                ->setParameter('medicalHistory', $medicalHistory);
                        }
                    },
                ]
            );
    }
}