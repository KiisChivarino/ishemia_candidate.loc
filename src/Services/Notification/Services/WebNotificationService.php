<?php

namespace App\Services\Notification\Services;

use App\Entity\ChannelType;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\WebChannelService;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Отправка web уведомлений
 * Class WebNotificationService
 * @package App\Services\Notification
 */
class WebNotificationService extends NotificationService
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
     * @throws Exception
     */
    public function notify(): bool
    {
        $channelType = $this->em->getRepository(ChannelType::class)->findByName($this->channelType);

        if($channelType === null){
            throw new RuntimeException('Cannot find notification type "'. $this->channelType .'" in db!');
        }
        $notification = $this->createNotification()->setWebNotification(
            $this->channel->createWebNotification($this->notificationData->getPatientReceiver(), $channelType)
        );
        $notification->setChannelType($channelType);
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }
}