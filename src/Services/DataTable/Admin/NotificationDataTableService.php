<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\Notification;
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
 * Class NotificationDataTableService
 * @package App\Services\DataTable\Admin
 */
class NotificationDataTableService extends AdminDatatableService
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
                'notificationType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationType')
                ]
            )
            ->add(
                'from', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var AuthUser $authUser */
                        $authUser = $notification->getFrom();
                        return $authUser ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($authUser, true),
                            $authUser->getId(),
                            'auth_user_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'patient', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('patient'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var Patient $patient */
                        $patient = $notification->getPatient();
                        return $patient ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($patient->getAuthUser(), true),
                            $patient->getId(),
                            'patient_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                ]
            )
            ->add(
                'notificationTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationTime'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:i'
                ]
            )
        ;

        /** @var Patient $patient */
        $patient = isset($filters[AppAbstractController::FILTER_LABELS['PATIENT']]) ? $filters[AppAbstractController::FILTER_LABELS['PATIENT']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Notification::class,
                    'query' => function (QueryBuilder $builder) use ($patient) {
                        $builder
                            ->select('n')
                            ->from(Notification::class, 'n');
                        if ($patient) {
                            $builder
                                ->andWhere('n.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                    },
                ]
            );
    }
}