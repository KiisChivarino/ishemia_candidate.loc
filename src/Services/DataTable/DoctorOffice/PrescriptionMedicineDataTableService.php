<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\TemplateItems\ShowTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionMedicineDataTableService
 * @package App\Services\DataTable\DoctorOffice
 */
class PrescriptionMedicineDataTableService extends AdminDatatableService
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
//        $this->dataTable->add(
//                'inclusionTime', DateTimeColumn::class, [
//                    'format' => 'd.m.Y',
//                    'searchable' => false
//                ]
//            );

        $this->addOperations($renderOperationsFunction, $showTemplateItem);

        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => self::ENTITY_CLASS,
                    'query' => function (QueryBuilder $builder) use ($prescription) {
                            $builder
                                ->select('pm')
                                ->from(self::ENTITY_CLASS, 'pm')
                            ;
//                        if ($prescription) {
//                            $builder
//                                ->andWhere('pt.prescription = :prescription')
//                                ->setParameter('prescription', $prescription);
//                        }
                    },
                ]
            );
    }

}