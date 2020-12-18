<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Notification;
use App\Entity\SMSNotification;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 * @package App\DataTable
 */
class SMSNotificationDataTableService extends AdminDatatableService
{
    /**
     * Таблица sms уведомлений
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