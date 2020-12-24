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
    ): DataTable {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'authUserSender', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var AuthUser $authUser */
                        $authUser = $notification->getAuthUserSender();
                        return $authUser ? $this->getLink(
                            (new AuthUserInfoService())->getFIO($authUser, true),
                            $authUser->getId(),
                            'auth_user_show'
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
            ->add(
                'notificationReceiverType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationReceiverType'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var NotificationReceiverType $notificationReceiverType */
                        $notificationReceiverType = $notification->getNotificationReceiverType();
                        return $notificationReceiverType ? $notificationReceiverType->getName() : '';
                    },
                ]
            )
            ->add(
                'receiver', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('receiver'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var NotificationReceiverType $notificationReceiverType */
                        switch ($notification->getNotificationReceiverType()->getName()){
                            case 'patient':
                                $patientNotification = $notification->getPatientNotification();
                                return $patientNotification ? $this->getLink(
                                    (new AuthUserInfoService())->getFIO($patientNotification->getPatient()->getAuthUser(), true),
                                    $patientNotification->getPatient()->getId(),
                                    'patient_show'
                                ) : '';
                            case 'staff':
                            default:
                                return '';
                        }

                    },
                ]
            )
            ->add(
                'medicalHistory', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalHistory'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var MedicalHistory $medicalHistory */
                        $medicalHistory = $notification->getPatientNotification()->getMedicalHistory();
                        return $medicalHistory ? $this->getLink(
                            'История болезни',
                            $medicalHistory->getId(),
                            'medical_history_show'
                        ) : '-';
                    },
                ]
            )
            ->add(
                'medicalRecord', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('medicalRecord'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var MedicalRecord $medicalRecord */
                        $medicalRecord = $notification->getPatientNotification()->getMedicalRecord();
                        return $medicalRecord ? $this->getLink(
                            'Запись в историю болезни',
                            $medicalRecord->getId(),
                            'medical_record_show'
                        ) : '-';
                    },
                ]
            )
        ;

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
                            ->addSelect('pN')
                        ;
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