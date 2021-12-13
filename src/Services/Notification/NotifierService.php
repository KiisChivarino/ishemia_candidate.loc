<?php

namespace App\Services\Notification;

use App\Services\Notification\Services\EmailNotificationService;
use App\Services\Notification\Services\SMSNotificationService;
use App\Services\Notification\Services\WebNotificationService;
use Exception;

/**
 * Отправка уведомлений пользователю
 * Class NotifierService
 * @package App\Services\Notification
 */
class NotifierService
{
    /**
     * @var array
     * yaml:config/services/notifications/notification_receiver_types.yaml
     */
    private $notificationReceiverTypeNames;

    /**
     * NotifierService constructor.
     * @param array $notificationReceiverTypeNames
     */
    public function __construct(array $notificationReceiverTypeNames)
    {
        $this->notificationReceiverTypeNames = $notificationReceiverTypeNames;
    }

    /**
     * Notification sender for patient
     * @param WebNotificationService $webNotificationService
     * @param SMSNotificationService $smsNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @return void
     * @throws Exception
     */
    public function notifyPatient(
        WebNotificationService $webNotificationService,
        SMSNotificationService $smsNotificationService,
        EmailNotificationService $emailNotificationService
    ): void
    {
        $webNotificationService->setNotificationReceiverType($this->notificationReceiverTypeNames['patient'])->notify();

        if ($smsNotificationService->getNotificationData()->getPatientReceiver()->getSmsInforming()) {
            $smsNotificationService->setNotificationReceiverType($this->notificationReceiverTypeNames['patient'])->notify();
        }

        if (
            $emailNotificationService->getNotificationData()->getPatientReceiver()->getEmailInforming()
            && !is_null($smsNotificationService->getNotificationData()->getPatientReceiver()->getAuthUser()->getEmail())
        ) {
            $emailNotificationService->setNotificationReceiverType($this->notificationReceiverTypeNames['patient'])->notify();
        }
    }
}