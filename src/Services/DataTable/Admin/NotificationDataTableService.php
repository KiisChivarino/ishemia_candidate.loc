<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class NotificationDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class NotificationDataTableService extends AdminDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem, array $filters): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'notificationType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationType'),
                    'render' => function (string $data, Notification $notification) {
                        /** @var NotificationType $notificationType */
                        $notificationType = $notification->getNotificationType();
                        return
                            $notificationType ? $this->getLink($notificationType->getName(), $notificationType->getId(), 'notification_type_show') : '';
                    },
                ]
            )
            ->add(
                'medicalRecord', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalRecord'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var MedicalRecord $medicalRecord */
                        $medicalRecord = $notification->getMedicalRecord();
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
                    'render' => function (string $data, Notification $notification): string {
                        /** @var Staff $staff */
                        $staff = $notification->getStaff();
                        return $staff ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true),
                            $staff->getId(),
                            'staff_show'
                        ) : '';
                    },
                ]
            )
            ->add(
                'notificationTime', DateTimeColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationTime'),
                    'searchable' => false,
                    'format' => 'd.m.Y H:i'
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = isset($filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']]) ? $filters[AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Notification::class,
                    'query' => function (QueryBuilder $builder) use ($medicalHistory) {
                        $builder
                            ->select('n')
                            ->from(Notification::class, 'n');
                        if ($medicalHistory) {
                            $builder
                                ->andWhere('n.medicalHistory = :medicalHistory')
                                ->setParameter('medicalHistory', $medicalHistory);
                        }
                    },
                ]
            );
    }
}