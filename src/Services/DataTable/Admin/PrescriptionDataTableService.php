<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class PrescriptionDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class PrescriptionDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param $filters
     *
     * @return DataTable
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'medicalHistory', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalHistory'),
                    'render' => function ($dataString, $prescription) {
                        /** @var MedicalHistory $medicalHistory */
                        $medicalHistory = $prescription->getMedicalHistory();
                        return $medicalHistory ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($medicalHistory->getPatient()->getAuthUser(), true)
                            .': '.
                            $medicalHistory->getDateBegin()->format('d.m.Y'),
                            $medicalHistory->getId(),
                            'medical_history_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'staffFio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staffFio'),
                    'render' => function ($dataString, $prescription) {
                        /** @var Staff $staff */
                        $staff = $prescription->getStaff();
                        return $staff ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true),
                            $staff->getId(),
                            'staff_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'createdTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('createdTime'),
                    'format' => 'd.m.Y H:i',
                    'searchable' => false
                ]
            )
            ->add(
                'isCompleted', BoolColumn::class, [
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'label' => $listTemplateItem->getContentValue('isCompleted'),
                    'searchable' => false,
                ]
            )
            ->add(
                'isPatientConfirmed', BoolColumn::class, [
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'label' => $listTemplateItem->getContentValue('isPatientConfirmed'),
                    'searchable' => false,
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = isset($filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']]) ? $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']] : null;
        /** @var Staff $staff */
        $staff = isset($filters[AppAbstractController::FILTER_LABELS['STAFF']]) ? $filters[AppAbstractController::FILTER_LABELS['STAFF']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Prescription::class,
                    'query' => function (QueryBuilder $builder) use ($medicalHistory, $staff) {
                        $builder
                            ->select('p')
                            ->from(Prescription::class, 'p');
                        if ($medicalHistory) {
                            $builder
                                ->andWhere('p.medicalHistory = :medicalHistory')
                                ->setParameter('medicalHistory', $medicalHistory);
                        }
                        if ($staff) {
                            $builder
                                ->andWhere('p.staff = :staff')
                                ->setParameter('staff', $staff);
                        }
                    },
                ]
            );
    }
}