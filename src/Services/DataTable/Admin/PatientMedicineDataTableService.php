<?php

namespace App\Services\DataTable\Admin;

use App\Entity\MedicalHistory;
use App\Entity\PatientMedicine;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PatientMedicineDataTableService
 * @package App\Services\DataTable\Admin
 */
class PatientMedicineDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'medicalHistory', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalHistory'),
                    'render' => function ($dataString, $medicalRecord) {
                        /** @var MedicalHistory $medicalHistory */
                        $medicalHistory = $medicalRecord->getMedicalHistory();
                        return $medicalHistory ? $this->getLink(
                            (new MedicalHistoryInfoService())->getMedicalHistoryTitle($medicalHistory),
                            $medicalHistory->getId(),
                            'medical_history_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'medicineName', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicineName'),
                ]
            )
            ->add(
                'dateBegin', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateBegin'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
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