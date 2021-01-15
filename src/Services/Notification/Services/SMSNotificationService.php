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
    /** @var SMSChannelService */
    private $channel;

    /**
     * SMSNotificationService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param SMSChannelService $smsChannelService
     * @param array $channelTypes
     * @param array $notificationReceiverTypes
     */
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
        $this->channelType = $this->CHANNEL_TYPES['sms-beeline'];
    }

    /**
     * Sends SMS notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification();
        $notification
            ->setSmsNotification(
                $this->channel
                    ->setText($notification->getText())
                    ->setAuthUser($this->patientReceiver->getAuthUser())
                    ->sendSMS()
            );
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->channelType)
        );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }
}