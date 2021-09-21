<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\ChannelType;
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
class NotificationDataTableService extends DoctorOfficeDatatableService
{
    /**
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @param array $filters
     * @param array $options
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters,
        array $options
    ): DataTable {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'authUserSender', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var AuthUser $authUser */
                        $authUser = $notification->getAuthUserSender();
                        return $authUser ?
                            (new AuthUserInfoService())->getFIO($authUser, true)
                         : $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text'),
                    'render' => function (string $data, Notification $notification): string {
                        return strip_tags($notification->getText());
                    },
                ]
            )
            ->add(
                'channelType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('channelType'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var ChannelType $channelType */
                        $channelType = $notification->getChannelType();
                        return $channelType ?
                            $channelType->getName()
                            : $listTemplateItem->getContentValue('empty');
                    },
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
                'receiver', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('receiver'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var NotificationReceiverType $notificationReceiverType */
                        switch ($notification->getNotificationReceiverType()->getName()){
                            case 'patient':
                                $patientNotification = $notification->getPatientNotification();
                                return $patientNotification ? $this->getLink(
                                    (new AuthUserInfoService())->getFIO(
                                        $patientNotification->getPatient()->getAuthUser(), true
                                    ),
                                    $patientNotification->getPatient()->getId(),
                                    'doctor_medical_history'
                                ) : $listTemplateItem->getContentValue('empty');
                            case 'staff':
                            default:
                                return $listTemplateItem->getContentValue('empty');
                        }

                    },
                    'field' => 'AU.lastName'
                ]
            )
        ;

        /** @var Patient $patient */
        $channelType = $filters[AppAbstractController::FILTER_LABELS['CHANNEL_TYPE']] ?? null;
        if ($filters[AppAbstractController::FILTER_LABELS['HOSPITAL']] !== "") {
            $hospital = $filters[AppAbstractController::FILTER_LABELS['HOSPITAL']];
        } elseif ($options) {
            $hospital = $options['hospital'];
        } else {
            $hospital = "";
        }
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => Notification::class,
                    'query' => function (QueryBuilder $builder) use ($channelType, $hospital) {
                        $builder
                            ->select('n')
                            ->from(Notification::class, 'n')
                            ->leftJoin('n.patientNotification', 'pN')
                            ->leftJoin('pN.patient', 'p')
                            ->leftJoin('p.AuthUser', 'AU')
                            ->addSelect('pN')
                        ;
                        if ($channelType) {
                            $builder
                                ->andWhere('n.channelType = :channelType')
                                ->setParameter('channelType', $channelType);
                        }
                        if ($hospital) {
                            $builder
                                ->andWhere('p.hospital = :hospital')
                                ->setParameter('hospital', $hospital);
                        }
                    },
                ]
            );
    }
}