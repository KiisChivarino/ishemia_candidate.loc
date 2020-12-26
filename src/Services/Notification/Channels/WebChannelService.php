<?php


namespace App\Services\Notification\Channels;

use App\Entity\ChannelType;
use App\Entity\Patient;
use App\Entity\WebNotification;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Работа с каналом web уведомлений
 * Class WebChannelService
 * @package App\Services\Notification
 */
class WebChannelService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * WebChannelService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Creates new WebNotification
     * @param $patientReceiver
     * @param $channel
     * @return WebNotification
     */
    public function createWebNotification(Patient $patientReceiver, ChannelType $channel): WebNotification
    {
        $webNotification = (new WebNotification())
            ->setReceiverString((new AuthUserInfoService())->getFIO($patientReceiver->getAuthUser()))
            ->setChannelType($channel)
        ;

        $this->em->persist($webNotification);
        return $webNotification;
    }
}