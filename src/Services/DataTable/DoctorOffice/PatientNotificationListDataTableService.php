<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\Notification;
use App\Entity\NotificationConfirm;
use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class PatientNotificationListDataTableService extends DoctorOfficeDatatableService
{
    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
    }

    /**
     * Таблица уведомлений в кабинете врача
     *
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
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'notificationType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notificationType'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        return $listTemplateItem->getContentValue($notification->getNotificationTemplate()->getName());
                    },
                ]
            )
            ->add(
                'authUserSender', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var AuthUser $authUser */
                        $authUser = $notification->getAuthUserSender();
                        return (new AuthUserInfoService())->getFIO($authUser, true);
                    },
                    'searchable' => true,
                ]
            )
            ->add(
                'first_name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'field' => 'upper(aU.firstName)',
                    'searchable' => true,
                    'visible' => false
                ]
            )
            ->add(
                'last_name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('staff'),
                    'field' => 'upper(aU.lastName)',
                    'searchable' => true,
                    'visible' => false
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
                'channels', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('channels'),
                    'render' => function (string $data, Notification $notification): string {
                        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(
                            [
                                'groupId' => $notification->getGroupId()
                            ]
                        );
                        $channels = "";
                        /** @var Notification $notification */
                        foreach ($notifications as $notification) {
                            if ($notification->getWebNotification()) {
                                $webNotification = $notification->getWebNotification()->getNotification();
                                $channels .= $this->getLinkMultiParam(
                                        "web",
                                        [
                                            'notification' => $webNotification->getId(),
                                            'patient' => $webNotification->getPatientNotification()->getPatient()->getId(),
                                        ],
                                        'doctor_office_patient_notification_show'
                                    ) . "<br>";
                            }
                            elseif ($notification->getEmailNotification()) {
                                $emailNotification = $notification->getEmailNotification()->getNotification();
                                $channels .= $this->getLinkMultiParam(
                                        "email",
                                        [
                                            'notification' => $emailNotification->getId(),
                                            'patient' => $emailNotification->getPatientNotification()->getPatient()->getId(),
                                        ],
                                        'doctor_office_patient_notification_show'
                                    ) . "<br>";
                            }
                            elseif ($notification->getSmsNotification()) {
                                $smsNotification = $notification->getSmsNotification()->getNotification();
                                $channels .= $this->getLinkMultiParam(
                                        "sms",
                                        [
                                            'notification' => $smsNotification->getId(),
                                            'patient' => $smsNotification->getPatientNotification()->getPatient()->getId(),
                                        ],
                                        'doctor_office_patient_notification_show'
                                    ) . "<br>";
                            }
                        }
                        return $channels;
                    },
                ]
            )
            ->add(
                'Status', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('Status'),
                    'render' => function (string $data, Notification $notification) use ($listTemplateItem): string {
                        /** @var NotificationConfirm $notificationConfirm */
                        $notificationConfirm = $notification->getPatientNotification()->getNotificationConfirm();

                        return $notificationConfirm->getIsConfirmed()
                            ? $listTemplateItem->getContentValue('confirmed')
                            : $listTemplateItem->getContentValue('sent');
                    },
                ]
            )
        ;
        $notificationTemplate = $filters[AppAbstractController::FILTER_LABELS['NOTIFICATION']];
        /** @var Patient $patient */
        $patient = $options['patient'];
        return
            $this->dataTable
                ->createAdapter(
                    ORMAdapter::class, [
                        'entity' => Notification::class,
                        'query' => function (QueryBuilder $builder) use ($notificationTemplate, $patient) {
                            $builder
                                ->select('n')
                                ->from(Notification::class, 'n')
                                ->leftJoin('n.patientNotification', 'pN')
                                ->addSelect('pN')
                                ->innerJoin('n.webNotification', 'wN')
                                ->addSelect('wN')
                                ->innerJoin('n.authUserSender', 'aU')
                                ->addSelect('aU')
                                ->andWhere('pN.patient = :patient')
                                ->setParameter('patient', $patient)
                                ->orderBy('n.notificationTime', 'DESC');
                            if ($notificationTemplate) {
                                $builder
                                    ->andWhere('n.notificationTemplate = :notificationTemplate')
                                    ->setParameter('notificationTemplate', $notificationTemplate);
                            }
                        },
                        'criteria' => $this->criteriaSearch(),
                    ]
                );
    }
}