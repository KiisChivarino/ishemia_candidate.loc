<?php


namespace App\Services\Notification\Services;

use App\Entity\ChannelType;
use App\Entity\EmailNotification;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\EmailChannelService;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Создание email уведомлений
 * Class EmailNotificationService
 * @package App\Services\Notification
 */
class EmailNotificationService extends NotificationService
{
    /** @var EmailChannelService */
    private $channel;

    /**
     * EmailNotificationService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param EmailChannelService $emailChannelService
     * @param array $channelTypes
     * @param array $notificationReceiverTypes
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        EmailChannelService $emailChannelService,
        array $channelTypes,
        array $notificationReceiverTypes
    ) {
        parent::__construct($em, $tokenStorage, $logService, $translator, $channelTypes, $notificationReceiverTypes);
        $this->channel = $emailChannelService;
    }

    /**
     * Send Email notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification($this->CHANNEL_TYPES['email']);
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->CHANNEL_TYPES['email'])
        );
        $emailNotification = new EmailNotification();
        $emailNotification->setPatientRecipientEmail($this->patientReceiver->getAuthUser()->getEmail());
        $emailNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->CHANNEL_TYPES['email'])
        );

        try {
            $this->channel
                ->setPatient($this->patientReceiver)
                ->setHeader('Добрый день!')
                ->setContent($notification->getText())
                ->sendDefaultEmail();
            $this->em->persist($emailNotification);
            $this->logger
                ->setUser($this->userSender)
                ->setDescription(
                    $this->translator->trans(
                        'log.new.entity',
                        ['%entity%' => 'Email уведомление', '%id%' => $emailNotification->getId()]
                    )
                )
                ->logSuccessEvent();
        } catch (ErrorException | LoaderError | RuntimeError | SyntaxError $e) {
            $this->logger
                ->setUser($this->userSender)
                ->setDescription($e)
                ->logErrorEvent();
        }

        $notification->setEmailNotification($emailNotification);
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);

        return true;
    }
}