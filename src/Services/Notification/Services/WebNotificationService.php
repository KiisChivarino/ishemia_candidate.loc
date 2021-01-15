<?php

namespace App\Services\Notification\Services;

use App\Entity\ChannelType;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\WebChannelService;
use App\Services\Notification\NotificationInterface;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Отправка web уведомлений
 * Class WebNotificationService
 * @package App\Services\Notification
 */
class WebNotificationService extends NotificationService implements NotificationInterface
{
    /** @var WebChannelService */
    private $channel;

    /**
     * WebNotificationService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param WebChannelService $webChannelService
     * @param array $channelTypes
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        WebChannelService $webChannelService,
        array $channelTypes
    )
    {
        parent::__construct($em, $tokenStorage, $logService, $translator, $channelTypes);
        $this->channel = $webChannelService;
        $this->channelType = $this->CHANNEL_TYPES['web'];
    }

    /**
     * Notify user via Web channel
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification()->setWebNotification(
            $this->channel->createWebNotification(
                $this->notificationData->getPatientReceiver(),
                $this->em->getRepository(ChannelType::class)->findByName($this->channelType)
            )
        );
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->channelType)
        );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }
}