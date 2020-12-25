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
     * @param string $systemUserPhone
     * @param EmailChannelService $emailChannelService
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        string $systemUserPhone,
        EmailChannelService $emailChannelService
    )
    {
        parent::__construct($em, $tokenStorage, $logService, $translator, $systemUserPhone);
        $this->channel = $emailChannelService;
    }

    /**
     * Send Email notification
     * @return bool
     */
    public function notify(): bool
    {
        $notification = $this->createNotification(self::EMAIL_CHANNEL);
        $emailNotification = new EmailNotification();
        $emailNotification->setPatientRecipientEmail($this->patientReceiver->getAuthUser()->getEmail());
        $emailNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName(self::EMAIL_CHANNEL)
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