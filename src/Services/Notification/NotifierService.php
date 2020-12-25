<?php

namespace App\Services\Notification;

use App\Services\Notification\Services\EmailNotificationService;
use App\Services\Notification\Services\SMSNotificationService;
use App\Services\Notification\Services\WebNotificationService;

/**
 * Отправка уведомлений пользователю
 * Class NotifierService
 * @package App\Services\Notification
 */
class NotifierService
{
    /**
     * Notification sender for patient
     * @param WebNotificationService $webNotificationService
     * @param SMSNotificationService $smsNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @return void
     */
    public function notifyPatient(
        WebNotificationService $webNotificationService,
        SMSNotificationService $smsNotificationService,
        EmailNotificationService $emailNotificationService
    ): void
    {
        $webNotificationService->setNotificationReceiverType(NotificationService::RECEIVER_TYPE_PATIENT)->notify();

        if ($smsNotificationService->getPatient()->getSmsInforming()) {
            $smsNotificationService->setNotificationReceiverType(NotificationService::RECEIVER_TYPE_PATIENT)->notify();
        }

        if ($emailNotificationService->getPatient()->getEmailInforming()) {
            $emailNotificationService->setNotificationReceiverType(NotificationService::RECEIVER_TYPE_PATIENT)->notify();
        }
    }
}