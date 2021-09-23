<?php

namespace App\Services\DataTable\Admin;

use App\Entity\Notification;
use App\Entity\WebNotification;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class WebNotificationDataTableService extends AdminDatatableService
{
    /**
     * Таблица web уведомлений
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
                    'render' => function (string $data, WebNotification $webNotification) {
                        /** @var Notification $notification */
                        $notification = $webNotification->getNotification();
                        return $notification
                            ? $this->getLink($notification->getText(), $notification->getId(), 'notification_show')
                            : '';
                    }
                ]
            )
            ->add(
                'receiverString', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('receiver'),
                ]
            )
            ->add(
                'isRead', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('isRead'),
                    'render' => function (string $data) use ($listTemplateItem) {
                       return ((boolean)$data == 'true')
                           ? $listTemplateItem->getContentValue('isReadFalse')
                           : $listTemplateItem->getContentValue('isReadTrue') ;
                    }
                ]
            );
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => WebNotification::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('w')
                            ->from(WebNotification::class, 'w')
                            ->leftJoin('w.notification', 'notification')
                            ->orderBy('w.id', 'desc');
                    },
                ]
            );
    }
}