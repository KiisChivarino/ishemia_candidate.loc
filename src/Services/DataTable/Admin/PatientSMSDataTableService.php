<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Entity\PatientSMS;
use App\Services\InfoService\AuthUserInfoService;
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
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientSMSDataTableService extends AdminDatatableService
{
    /**
     * Таблица полученных sms
     *
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
                    'field' => 'u.lastName',
                    'orderable' => true,
                    'orderField' => 'u.lastName',
                    'render' => function (string $data, PatientSMS $patientSMS) use ($listTemplateItem) {
                        /** @var Patient $patient */
                        $patient = $patientSMS->getPatient();
                        return $patient ? $this->getLinkMultiParam(
                            AuthUserInfoService::getFIO($patient->getAuthUser(), true),
                            [
                                'patient' => $patient->getId(),
                            ],
                            'patient_show'
                        ) : $listTemplateItem->getContentValue('empty');
                    }
                ]
            )
            ->add(
                'phone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('phone'),
                    'render' => function (string $data, PatientSMS $patientSMS) {
                        /** @var Patient $patient */
                        $patient = $patientSMS->getPatient();
                        return (new AuthUserInfoService())->getPhone($patient->getAuthUser());
                    }
                ]
            )
            ->add(
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                ]
            )
            ->add(
                'isProcessed', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isProcessed'),
                    'searchable' => false,
                ]
            )
            ->add(
                'created_at', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('createdAt'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:m',
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);

        /** @var Patient $patient */
        $patient = $filters[AppAbstractController::FILTER_LABELS['PATIENT']] ?? null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => PatientSMS::class,
                    'query' => function (QueryBuilder $builder) use ($patient) {
                        $builder
                            ->select('ps')
                            ->from(PatientSMS::class, 'ps')
                            ->leftJoin('ps.patient', 'p')
                            ->leftJoin('p.AuthUser', 'u')
                            ->andWhere('u.enabled = :val')
                            ->setParameter('val', true)
                            ->orderBy('ps.id', 'desc');
                        if ($patient) {
                            $builder
                                ->andWhere('ps.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                    },
                ]
            );
    }
}