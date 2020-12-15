<?php

namespace App\Services\DataTable\Admin;

use App\Entity\EmailNotification;
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
 *
 * @package App\DataTable
 */
class EmailNotificationDataTableService extends AdminDatatableService
{
    /**
     * Таблица e-mail уведомлений
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
                    'render' => function (string $data, EmailNotification $emailNotification) {
                        /** @var Notification $notification */
                        $notification = $emailNotification->getNotification();
                        return $notification
                            ? $this->getLink($notification->getText(), $notification->getId(), 'log_action_show')
                            : '';
                    }
                ]
            )
            ->add(
                'emailTo', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('emailTo'),
                ]
            )
            ;

        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => EmailNotification::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('e')
                            ->from(EmailNotification::class, 'e')
                            ->leftJoin('e.notification', 'notification')
                            ->orderBy('e.id', 'desc')
                        ;
                    },
                ]
            );
    }
}