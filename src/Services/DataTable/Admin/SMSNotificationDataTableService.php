<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Notification;
use App\Entity\SMSNotification;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DataTableService
 * methods for adding data tables
 * @package App\DataTable
 */
class SMSNotificationDataTableService extends AdminDatatableService
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * SMSNotificationDataTableService constructor.
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface $router
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface|null $entityManager
     */
    public function __construct(
        DataTableFactory $dataTableFactory,
        UrlGeneratorInterface $router,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager = null)
    {
        parent::__construct($dataTableFactory, $router, $entityManager);
        $this->translator = $translator;
    }

    /**
     * Таблица sms уведомлений
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'notification', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('notification'),
                    'field' => 'notification.text',
                    'orderable' => true,
                    'orderField' => 'notification.text',
                    'render' => function (string $data, SMSNotification $smsNotification) {
                        /** @var Notification $notification */
                        $notification = $smsNotification->getNotification();
                        return $notification
                            ? $this->getLink($notification->getText(), $notification->getId(), 'notification_show')
                            : '';
                    }
                ]
            )
            ->add(
                'smsPatientRecipientPhone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('smsTo'),
                ]
            )
            ->add(
                'externalId', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('externalId'),
                ]
            )
            ->add(
                'status', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('status'),
                    'render' => function (string $data) {
                        return $this->translator->trans("sms_type.".$data);
                    }
                ]
            )
            ->add(
                'attemptCount', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('attempt'),
                    'searchable' => false,
                ]
            )
            ;

        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => SMSNotification::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('s')
                            ->from(SMSNotification::class, 's')
                            ->leftJoin('s.notification', 'notification')
                            ->orderBy('s.id', 'desc')
                        ;
                    },
                ]
            );
    }
}