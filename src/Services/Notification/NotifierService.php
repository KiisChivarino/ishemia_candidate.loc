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
    /** @var array */
    private $notificationReceiverTypes;

    /**
     * NotifierService constructor.
     * @param array $notificationReceiverTypes
     */
    public function __construct(array $notificationReceiverTypes)
    {
        $this->notificationReceiverTypes = $notificationReceiverTypes;
    }

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
        $webNotificationService->setNotificationReceiverType($this->notificationReceiverTypes['patient'])->notify();

        if ($smsNotificationService->getPatient()->getSmsInforming()) {
            $smsNotificationService->setNotificationReceiverType($this->notificationReceiverTypes['patient'])->notify();
        }

        if ($emailNotificationService->getPatient()->getEmailInforming()) {
            $emailNotificationService->setNotificationReceiverType($this->notificationReceiverTypes['patient'])->notify();
        }
    }
}