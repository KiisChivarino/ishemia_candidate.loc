<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class MedicalHistoryDataTableService
 *
 * @package App\Services\DataTable
 */
class MedicalHistoryDataTableService extends AdminDatatableService
{
    /**
     * Таблица списка историй болезни в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'patientFio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patientFio'),
                    'render' => function ($dataString, $medicalHistory) {
                        /** @var Patient $patient */
                        $patient = $medicalHistory->getPatient();
                        return $patient ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($patient->getAuthUser(), true),
                            $patient->getId(),
                            'patient_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'dateBegin', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateBegin'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            )
            ->add(
                'dateEnd', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('dateEnd'),
                    'format' => 'd.m.Y',
                    'searchable' => false
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var Patient $patient */
        $patient = $filters[AppAbstractController::FILTER_LABELS['PATIENT']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => MedicalHistory::class,
                    'query' => function (QueryBuilder $builder) use ($patient) {
                        $builder
                            ->select('m')
                            ->from(MedicalHistory::class, 'm');
                        if ($patient) {
                            $builder
                                ->andWhere('m.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                    },
                ]
            );
    }
}