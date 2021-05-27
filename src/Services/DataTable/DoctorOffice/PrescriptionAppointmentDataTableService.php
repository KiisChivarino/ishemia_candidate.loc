<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
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
 * Class PrescriptionAppointmentDataTableService
 * settings for display only one prescription for an appointment in doctor office
 * @package App\Services\DataTable\DoctorOffice
 */
class PrescriptionAppointmentDataTableService extends AdminDatatableService
{
    /** @var string class of main entity */
    public const ENTITY_CLASS = PrescriptionAppointment::class;

    public const DATATABLE_NAME = 'PrescritionAppointmentDataTable';

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
            ->setName(self::DATATABLE_NAME)
            ->add(
                'plannedDateTime', DateTimeColumn::class, [
                    'label' => $showTemplateItem->getContentValue('plannedDateTime'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            )
            ->add('appointmentType', TextColumn::class, [
                'label' => $showTemplateItem->getContentValue('appointmentType'),
                'field' => 'pta.appointmentType.name',
                'searchable' => true,
                'render' => function (string $data) use ($showTemplateItem) {
                    return
                        $data ?: $showTemplateItem->getContentValue('empty');
                }
            ]);
        $this->addOperations($renderOperationsFunction, $showTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => self::ENTITY_CLASS,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                        $builder
                            ->select('pa')
                            ->from(self::ENTITY_CLASS, 'pa')
                            ->join('pa.patientAppointment', 'pta');
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