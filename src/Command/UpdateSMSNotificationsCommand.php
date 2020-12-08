<?php


namespace App\Command;

use App\Entity\SMSNotification;
use App\Services\Notification\SMSNotificationService;
use ErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class UpdateSMSNotificationsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-sms';

    const MAX_ATTEMPTS = 1;
    const
        DELIVERED = 'delivered',
        NOT_DELIVERED = 'not_delivered',
        WAIT = 'wait',
        FAILED = 'failed'
    ;
    private $container;

    /**
     * @var SMSNotificationService
     */
    private $sms;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->container->get('doctrine')->getManager();

        $notifications = $em->getRepository(SMSNotification::class)->findBy([
            'status' => 'wait'
        ]);

        $result = $this->sms->checkSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            foreach ($notifications as $notification) {
                if ((string) $message['SMS_ID'] == (string) $notification->getExternalId() && $notification->getStatus() != self::NOT_DELIVERED) {
                    switch ((string)$message->SMSSTC_CODE) {
                        case self::DELIVERED:
                            $notification->setStatus(self::DELIVERED);
                            $em->persist($notification);
                            break;
                        case self::WAIT:
                            break;
                        case self::NOT_DELIVERED:
                        case self::FAILED:
                            if ($notification->getAttempt() <= self::MAX_ATTEMPTS || self::MAX_ATTEMPTS == '-1') {
                                $this->sms->resendSMS($notification);
                            } else {
                                $notification->setStatus(self::NOT_DELIVERED);
                                $em->persist($notification);
                            }
                            break;
                    }
                }
            }

        }

        $em->flush();
        return 0;
    }

}