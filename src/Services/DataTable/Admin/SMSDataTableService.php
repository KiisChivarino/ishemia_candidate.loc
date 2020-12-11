<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Patient;
use App\Entity\ReceivedSMS;
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
 * Class DataTableService
 * methods for adding data tables
 * @package App\DataTable
 */
class SMSDataTableService extends AdminDatatableService
{
    /**
     * Таблица полученных sms
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
                'patient', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patient'),
                    'field' => 'u.lastName',
                    'orderable' => true,
                    'orderField' => 'u.lastName',
                    'render' => function (string $data, ReceivedSMS $receivedSMS) {
                        /** @var Patient $patient */
                        $patient = $receivedSMS->getPatient();
                        return $patient
                            ? $this->getLink((new AuthUserInfoService())
                                ->getFIO($patient->getAuthUser()), $patient->getId(), 'patient_show')
                            : '';
                    }
                ]
            )
            ->add(
                'phone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('phone'),
                    'render' => function (string $data, ReceivedSMS $receivedSMS) {
                        /** @var Patient $patient */
                        $patient = $receivedSMS->getPatient();
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
                'created_at', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('createdAt'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:m',
                ]
            )
            ;
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => ReceivedSMS::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('l')
                            ->from(ReceivedSMS::class, 'l')
                            ->leftJoin('l.patient', 'p')
                            ->leftJoin('p.AuthUser', 'u')
                            ->andWhere('u.enabled = :val')
                            ->setParameter('val', true)
                            ->orderBy('l.id', 'desc')
                        ;
                    },
                ]
            );
    }
}