<?php


namespace App\Command;

use App\Entity\SMSNotification;
use App\Services\Notification\SMSNotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UpdateSMSNotificationsCommand
 * @package App\Command
 */
class UpdateSMSNotificationsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-sms';

    /** @var int Max attempts for resend sms */
    const MAX_ATTEMPTS = 1;

    /** @var string Standard sms statuses */
    const
        DELIVERED = 'delivered', // Статус sms - Доставлено
        NOT_DELIVERED = 'not_delivered', // Статус sms - Не доставлено
        WAIT = 'wait', // Статус sms - Ожидание доставки
        FAILED = 'failed' // Статус sms - Ошибка
    ;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SMSNotificationService
     */
    private $sms;

    /**
     * UpdateSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSNotificationService $SMSNotificationService
     */
    public function __construct(ContainerInterface $container, SMSNotificationService $SMSNotificationService)
    {
        parent::__construct();
        $this->container = $container;
        $this->sms = $SMSNotificationService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Check and update SMS notification`s statuses')
            ->setHelp('This command checks and update SMS notification`s statuses')
        ;
    }

    /**
     * Check sms for the past day, and depending on status change data in out database
     * and resend if message is not delivered
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->container->get('doctrine')->getManager();
        $notifications = $em->getRepository(SMSNotification::class)->findBy([
            'status' => self::WAIT
        ]);

        $result = $this->sms->checkSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            foreach ($notifications as $notification) {
                if (
                    (string) $message['SMS_ID'] == (string) $notification->getExternalId()
                    && $notification->getStatus() != self::NOT_DELIVERED
                ) {
                    switch ((string)$message->SMSSTC_CODE) {
                        case self::DELIVERED:
                            $notification->setStatus(self::DELIVERED);
                            $em->persist($notification);
                            break;
                        case self::WAIT:
                            break;
                        case self::NOT_DELIVERED:
                            if ($notification->getAttempt() <= self::MAX_ATTEMPTS || self::MAX_ATTEMPTS == '-1') {
                                $this->sms->resendSMS($notification);
                            } else {
                                $notification->setStatus(self::NOT_DELIVERED);
                                $em->persist($notification);
                            }
                            break;
                        case self::FAILED:
                            $notification->setStatus(self::FAILED);
                            $em->persist($notification);
                            break;
                    }
                }
            }
        }

        $em->flush();
        return 0;
    }

}