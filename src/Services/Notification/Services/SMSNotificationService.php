<?php

namespace App\Services\Notification\Services;

use App\Entity\ChannelType;
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
    /** Константы для sms провайдеров  */
    const
        SMS_PROVIDER_BEELINE = 'Beeline'
    ;

    /** @var SMSChannelService */
    private $channel;

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        SMSChannelService $smsChannelService,
        array $channelTypes,
        array $notificationReceiverTypes
    )
    {
        parent::__construct($em, $tokenStorage, $logService, $translator, $channelTypes, $notificationReceiverTypes);
        $this->channel = $smsChannelService;
    }

    /**
     * Sends SMS notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification($this->CHANNEL_TYPES['sms-beeline']);
        $notification
            ->setSmsNotification(
                $this->channel
                    ->setText($notification->getText())
                    ->setAuthUser($this->patientReceiver->getAuthUser())
                    ->setProvider(self::SMS_PROVIDER_BEELINE)
                    ->sendSMS()
            );
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->CHANNEL_TYPES['sms-beeline'])
        );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }
}