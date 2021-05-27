<?php

namespace App\Services\Notification\Services;

use App\Entity\ChannelType;
use App\Entity\EmailNotification;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\EmailChannelService;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    /** @var SessionInterface */
    private $session;

    /**
     * EmailNotificationService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param EmailChannelService $emailChannelService
     * @param SessionInterface $session
     * @param array $channelTypes
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        EmailChannelService $emailChannelService,
        SessionInterface $session,
        array $channelTypes
    )
    {
        parent::__construct($em, $tokenStorage, $logService, $translator, $channelTypes);
        $this->channel = $emailChannelService;
        $this->session = $session;
        $this->channelType = $this->CHANNEL_TYPES['email'];
    }

    /**
     * Send Email notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification();
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->channelType)
        );
        $emailNotification = new EmailNotification();
        $emailNotification->setPatientRecipientEmail($this->notificationData->getPatientReceiver()->getAuthUser()->getEmail());
        $emailNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($this->channelType)
        );
        try {
            $this->channel
                ->setPatient($this->notificationData->getPatientReceiver())
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
            $this->session
                ->getFlashBag()->add('error', 'Email сообщение не отправлено. Ошибка при отправке.');
            $this->logger
                ->setUser($this->userSender)
                ->setDescription($e->getMessage() . '      ' . $e->getTraceAsString())
                ->logErrorEvent();
        }
        $notification->setEmailNotification($emailNotification);
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);

        return true;
    }
}