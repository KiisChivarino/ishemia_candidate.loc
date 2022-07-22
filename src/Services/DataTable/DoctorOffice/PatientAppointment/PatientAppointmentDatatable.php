<?php

namespace App\Services\DataTable\DoctorOffice\PatientAppointment;

use App\Entity\AppointmentType;
use App\Entity\PatientAppointment;
use App\Entity\PatientTesting;
use App\Entity\Staff;
use App\Repository\PatientAppointmentDataTableRepository;
use App\Services\DataTable\DoctorOffice\DoctorOfficeDatatableService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\StatusService\Factory\PatientAppointment\AppointmentStatusFactory;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class PatientAppointmentDatatable extends DoctorOfficeDatatableService
{
    /** @var PatientAppointmentDataTableRepository */
    protected $patientAppointmentDatatableRepository;


    /** @var AppointmentStatusFactory $appointmentStatusFactory */
    private $appointmentStatusFactory;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     * @param PatientAppointmentDataTableRepository $patientAppointmentDatatableRepository
     * @param AppointmentStatusFactory $appointmentStatusFactory
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em,
        PatientAppointmentDataTableRepository $patientAppointmentDatatableRepository,
        AppointmentStatusFactory $appointmentStatusFactory
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
        $this->patientAppointmentDatatableRepository = $patientAppointmentDatatableRepository;
        $this->appointmentStatusFactory = $appointmentStatusFactory;
    }

    /**
     * Таблица приёмов в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array|null $filters
     * @param array $options
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        ?array $filters,
        array $options
    ): DataTable {
        $this->generateTableForPatientAppointmentInDoctorOffice(
            $renderOperationsFunction,
            $listTemplateItem
        );
        $this->addOperationsWithParameters(
            $listTemplateItem,
            function (int $patientAppointmentId, PatientAppointment $patientAppointment) use (
                $options,
                $renderOperationsFunction
            ) {
                return
                    $renderOperationsFunction(
                        (string)$patientAppointment->getMedicalHistory()->getPatient()->getId(),
                        $patientAppointment,
                        [
                            'patientAppointment' => $patientAppointmentId,
                            'patient' => (string)$patientAppointment->getMedicalHistory()->getPatient()->getId()
                        ]
                    );
            }
        );
        $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientTesting::class,
                    'query' => function (QueryBuilder $builder) use ($options) {
                        $this->getAppointments($builder, $options['patientId']);
                    }
                ]
            );
        return $this->dataTable;
    }

    /**
     * Returns appointments for datatable list
     *
     * @param QueryBuilder $builder
     * @param int $patientId
     * @return QueryBuilder
     */
    abstract protected function getAppointments(QueryBuilder $builder, int $patientId): QueryBuilder;

    /**
     * Generates table for patient appointment
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return void
     *
     * @throws Exception
     */
    protected function generateTableForPatientAppointmentInDoctorOffice(
        Closure          $renderOperationsFunction,
        ListTemplateItem $listTemplateItem
    ): void {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'staff', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment) use ($listTemplateItem): string {
                        /** @var Staff $staff */
                        $staff = $patientAppointment->getStaff();
                        return $staff
                            ? (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true)
                            : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'appointmentType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('appointmentType'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment) use ($listTemplateItem): string {
                        /** @var AppointmentType $appointmentType */
                        $appointmentType = $patientAppointment->getAppointmentType();
                        return $appointmentType
                            ? $appointmentType->getName()
                            : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'plannedDateTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('plannedDateTime'),
                    'field' => 'prA.plannedDateTime',
                    'searchable' => false,
                    'format' => 'd.m.Y',
                    'nullValue' => $listTemplateItem->getContentValue('empty'),
                ]
            )
            ->add(
                'appointmentTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('appointmentTime'),
                    'field' => 'paT.appointmentTime',
                    'searchable' => false,
                    'format' => 'd.m.Y',
                    'nullValue' => $listTemplateItem->getContentValue('empty')
                ]
            )
            ->add(
                'isByPlan', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isByPlan'),
                    'trueValue' => $listTemplateItem->getContentValue('isByPlanTrue'),
                    'falseValue' => $listTemplateItem->getContentValue('isByPlanFalse'),
                    'searchable' => false,
                ]
            )
            ->add(
                'status', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('status'),
                    'render' => function (string $dataString, PatientAppointment $patientAppointment) use ($listTemplateItem): string {
                        $appointmentStatus = $this->appointmentStatusFactory->getStatus($patientAppointment);
                        return '<div class="' . $appointmentStatus->getStatusRender()->getColor() . '">
                            ' . $appointmentStatus->getStatusRender()->getText() . '
                            </div>';
                    },
                ]
            );
    }
}
