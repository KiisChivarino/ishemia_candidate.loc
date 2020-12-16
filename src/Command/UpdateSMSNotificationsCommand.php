<?php


namespace App\Command;

use App\Entity\SMSNotification;
use App\Services\Notification\SMSNotificationService;
use Exception;
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

    /** @var ContainerInterface */
    private $container;

    /** @var SMSNotificationService */
    private $sms;

    /** @var array */
    private $smsStatuses;

    /**
     * UpdateSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSNotificationService $SMSNotificationService
     * @param array $smsStatuses
     */
    public function __construct(
        ContainerInterface $container,
        SMSNotificationService $SMSNotificationService,
        array $smsStatuses)
    {
        parent::__construct();
        $this->container = $container;
        $this->sms = $SMSNotificationService;
        $this->smsStatuses = $smsStatuses;
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->container->get('doctrine')->getManager();
        $notifications = $em->getRepository(SMSNotification::class)->findBy([
            'status' => $this->smsStatuses['wait']
        ]);

        $result = $this->sms->checkSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            foreach ($notifications as $notification) {
                if (
                    (string) $message['SMS_ID'] == (string) $notification->getExternalId()
                    && $notification->getStatus() != $this->smsStatuses['not_delivered']
                ) {
                    switch ((string)$message->SMSSTC_CODE) {
                        case $this->smsStatuses['delivered']:
                            $notification->setStatus($this->smsStatuses['delivered']);
                            $em->persist($notification);
                            break;
                        case $this->smsStatuses['wait']:
                            break;
                        case $this->smsStatuses['not_delivered']:
                            if ($notification->getAttempt() <= self::MAX_ATTEMPTS || self::MAX_ATTEMPTS == '-1') {
                                $this->sms->resendSMS($notification);
                            } else {
                                $notification->setStatus($this->smsStatuses['not_delivered']);
                                $em->persist($notification);
                            }
                            break;
                        case $this->smsStatuses['failed']:
                            $notification->setStatus($this->smsStatuses['failed']);
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