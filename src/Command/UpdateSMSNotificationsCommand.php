<?php


namespace App\Command;

use App\Entity\AuthUser;
use App\Entity\SMSNotification;
use App\Services\LoggerService\LogService;
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

    /** @var LogService */
    private $logger;

    /** @var string */
    private $systemUserPhone;


    /**
     * UpdateSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSNotificationService $SMSNotificationService
     * @param LogService $logger
     * @param array $smsStatuses
     * @param string $systemUserPhone
     */
    public function __construct(
        ContainerInterface $container,
        SMSNotificationService $SMSNotificationService,
        LogService $logger,
        array $smsStatuses,
        string $systemUserPhone
    ) {
        parent::__construct();
        $this->container = $container;
        $this->sms = $SMSNotificationService;
        $this->logger = $logger;
        $this->smsStatuses = $smsStatuses;
        $this->systemUserPhone = $systemUserPhone;
    }

    /**
     * Конфигурация для команды UpdateSMSNotifications
     */
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

        /** @var SMSNotification[] $smsNotifications */
        $smsNotifications = $em->getRepository(SMSNotification::class)->findBy([
            'status' => $this->smsStatuses['wait']
        ]);

        $result = $this->sms->checkSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            foreach ($smsNotifications as $smsNotification) {
                if (
                    (string) $message['SMS_ID'] == (string) $smsNotification->getExternalId()
                    && $smsNotification->getStatus() != $this->smsStatuses['not_delivered']
                ) {
                    switch ((string)$message->SMSSTC_CODE) {
                        case $this->smsStatuses['delivered']:
                            $smsNotification->setStatus($this->smsStatuses['delivered']);
                            $em->persist($smsNotification);
                            break;
                        case $this->smsStatuses['wait']:
                            break;
                        case $this->smsStatuses['not_delivered']:
                            if ($smsNotification->getAttemptCount() <= self::MAX_ATTEMPTS || self::MAX_ATTEMPTS == '-1') {
                                $this->sms->resendSMS($smsNotification);
                            } else {
                                $smsNotification->setStatus($this->smsStatuses['not_delivered']);
                                $this->logger
                                    ->setUser($em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->systemUserPhone]))
                                    ->setDescription('SMS Уведомление (id:'. $smsNotification->getId() . ') не доставлено.')
                                    ->logFailEvent();
                                $em->persist($smsNotification);
                            }
                            break;
                        case $this->smsStatuses['failed']:
                            $smsNotification->setStatus($this->smsStatuses['failed']);
                            $this->logger
                                ->setUser($em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->systemUserPhone]))
                                ->setDescription('SMS Уведомление (id:'. $smsNotification->getId() . ') не доставлено.')
                                ->logFailEvent();
                            $em->persist($smsNotification);
                            break;
                    }
                }
            }
        }
        $this->logger
            ->setUser($em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->systemUserPhone]))
            ->setDescription('Команда - '. self::$defaultName . ' успешно выполнена.')
            ->logSuccessEvent();

        $em->flush();
        return Command::SUCCESS;
    }

}