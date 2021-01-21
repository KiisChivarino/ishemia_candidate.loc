<?php

namespace App\Services\DataTable\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\Notification;
use App\Entity\NotificationConfirm;
use App\Services\DataTable\Admin\AdminDatatableService;
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
class NotificationsListDataTableService extends AdminDatatableService
{
    /** @var string[] Переводы названий типов уведомлений */
    const NOTIFICATION_TYPES = [
        "customMessage" => "Сообщение от врача",
        "doctorAppointment" => "Прием у врача",
        "confirmMedication" => "Подтверждение приема лекарств",
        "testingAppointment" => "Сдача анализов",
        "confirmAppointment" => "Подтверждение приема",
        "submitAnalysisResults" => "Результаты анализов"
    ];

    /** @var AuthUserInfoService */
    private $authUserInfoService;

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param EntityManagerInterface $em
     * @param AuthUserInfoService $authUserInfoService
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em,
        AuthUserInfoService $authUserInfoService
    )
    {
        parent::__construct($dataTableFactory, $router, $em);
        $this->authUserInfoService = $authUserInfoService;
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
                    'render' => function (string $data, Notification $notification): string {
                        return self::NOTIFICATION_TYPES[$notification->getNotificationTemplate()->getName()];
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
                        foreach ($notifications as $notification) {
                            if ($notification->getWebNotification()) {
                                $channels .= $notification ? $this->getLink(
                                        "web",
                                        $notification->getId(),
                                        'doctor_office_notification_show'
                                    ) . "<br>" : '';
                            } elseif ($notification->getEmailNotification()) {
                                $channels .= $notification ? $this->getLink(
                                        "email",
                                        $notification->getId(),
                                        'doctor_office_notification_show'
                                    ) . "<br>" : '';
                            } elseif ($notification->getSmsNotification()) {
                                $channels .= $notification ? $this->getLink(
                                        "sms",
                                        $notification->getId(),
                                        'doctor_office_notification_show'
                                    ) . "<br>" : '';
                            }
                        }
                        return $channels;
                    },
                ]
            )
            ->add(
                'Status', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('Status'),
                    'render' => function (string $data, Notification $notification): string {
                        /** @var NotificationConfirm $notificationConfirm */
                        $notificationConfirm = $notification->getPatientNotification()->getNotificationConfirm();

                        return $notificationConfirm->getIsConfirmed() ? 'Подтверждено' : 'Отправлено';
                    },
                ]
            )
        ;
        $notificationTemplate = $filters[AppAbstractController::FILTER_LABELS['NOTIFICATION']];
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
                                ->andWhere('pN.patient = :patient')
                                ->setParameter('patient', $patient)
                                ->orderBy('n.notificationTime', 'DESC');
                            if ($notificationTemplate) {
                                $builder
                                    ->andWhere('n.notificationTemplate = :notificationTemplate')
                                    ->setParameter('notificationTemplate', $notificationTemplate);
                            }
                        },
                    ]
                );
    }
}