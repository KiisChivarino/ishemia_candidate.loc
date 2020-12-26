<?php

namespace App\Services\Notification\Services;

use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\SMSChannelService;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Сервис отправки СМС сообщений
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class SMSNotificationService extends NotificationService
{
    /** @var SMSChannelService */
    private $channel;

    /**
     * SMSNotificationService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param string $systemUserPhone
     * @param SMSChannelService $smsChannelService
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        string $systemUserPhone,
        SMSChannelService $smsChannelService
    )
    {
        parent::__construct($em, $tokenStorage, $logService, $translator, $systemUserPhone);
        $this->channel = $smsChannelService;
    }

    /**
     * Sends SMS notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification(self::SMS_CHANNEL);
        $notification
            ->setSmsNotification(
                $this->channel
                    ->setText($notification->getText())
                    ->setAuthUser($this->patientReceiver->getAuthUser())
                    ->setProvider(self::SMS_PROVIDER_BEELINE)
                    ->sendSMS()
            );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }
}