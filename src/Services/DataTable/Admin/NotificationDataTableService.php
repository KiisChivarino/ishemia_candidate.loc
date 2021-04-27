<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Entity\NotificationReceiverType;
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
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'authUserSender', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var AuthUser $authUser */
                        $authUser = $notification->getAuthUserSender();
                        return $authUser
                            ? $this->adminOrManagerReturn(
                                $this->getLink(
                                    (new AuthUserInfoService())->getFIO($authUser, true),
                                    $authUser->getId(),
                                    'auth_user_show'
                                ),
                                (new AuthUserInfoService())->getFIO($authUser, true),
                                $listTemplateItem->getContentValue('empty')
                            )
                            : $listTemplateItem->getContentValue('empty');
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
            ->add(
                'notificationReceiverType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationReceiverType'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var NotificationReceiverType $notificationReceiverType */
                        $notificationReceiverType = $notification->getNotificationReceiverType();
                        return $notificationReceiverType ? $notificationReceiverType->getTitle() : '';
                    },
                ]
            )
            ->add(
                'receiver', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('receiver'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var NotificationReceiverType $notificationReceiverType */
                        switch ($notification->getNotificationReceiverType()->getName()) {
                            case 'patient':
                                $patientNotification = $notification->getPatientNotification();
                                return $patientNotification ? $this->getLink(
                                    (new AuthUserInfoService())->getFIO(
                                        $patientNotification->getPatient()->getAuthUser(), true
                                    ),
                                    $patientNotification->getPatient()->getId(),
                                    'patient_show'
                                ) : $listTemplateItem->getContentValue('empty');
                            case 'staff':
//                                TODO: добавить когда появится функционал отправки сообщения врачу
                            default:
                                return '';
                        }

                    },
                ]
            )
            ->add(
                'medicalHistory', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalHistory'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var MedicalHistory $medicalHistory */
                        $medicalHistory = $notification->getPatientNotification()->getMedicalHistory();
                        return $medicalHistory ? $this->getLink(
                            $listTemplateItem->getContentValue('medicalHistory'),
                            $medicalHistory->getId(),
                            'medical_history_show'
                        ) : '-';
                    },
                ]
            )
            ->add(
                'medicalRecord', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalRecord'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var MedicalRecord $medicalRecord */
                        $medicalRecord = $notification->getPatientNotification()->getMedicalRecord();
                        return $medicalRecord ? $this->getLink(
                            $listTemplateItem->getContentValue('medicalRecord'),
                            $medicalRecord->getId(),
                            'medical_record_show'
                        ) : '-';
                    },
                ]
            );

        /** @var Patient $patient */
        $patient = isset($filters[AppAbstractController::FILTER_LABELS['PATIENT']])
            ? $filters[AppAbstractController::FILTER_LABELS['PATIENT']] : null;
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Notification::class,
                    'query' => function (QueryBuilder $builder) use ($patient) {
                        $builder
                            ->select('n')
                            ->from(Notification::class, 'n')
                            ->leftJoin('n.patientNotification', 'pN')
                            ->leftJoin('n.notificationReceiverType', 'nRT')
                            ->andWhere('nRT.name = :notificationReceiverType')
                            ->setParameter('notificationReceiverType', 'patient')
                            ->addSelect('pN');
                        if ($patient) {
                            $builder
                                ->andWhere('pN.patient = :patient')
                                ->setParameter('patient', $patient);
                        }
                    },
                ]
            );
    }
}