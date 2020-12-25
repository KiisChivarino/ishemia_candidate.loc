<?php


namespace App\Command;

use App\Entity\AuthUser;
use App\Entity\SMSNotification;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\SMSChannelService;
use App\Services\Notification\Services\SMSNotificationService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /** @var int Option to resend sms unlimited times */
    const UNLIMITED_ATTEMPTS = false;

    /** @var ContainerInterface */
    private $container;

    /** @var SMSNotificationService */
    private $sms;

    /** @var array */
    private $smsStatuses;

    /** @var LogService */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * UpdateSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSChannelService $SMSNotificationService
     * @param LogService $logger
     * @param TranslatorInterface $translator
     * @param array $smsStatuses
     */
    public function __construct(
        ContainerInterface $container,
        SMSChannelService $SMSNotificationService,
        LogService $logger,
        TranslatorInterface $translator,
        array $smsStatuses
    ) {
        parent::__construct();
        $this->container = $container;
        $this->sms = $SMSNotificationService;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->smsStatuses = $smsStatuses;
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
        $systemUser = $em->getRepository(AuthUser::class)->getSystemUser();

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
                            if ($smsNotification->getAttemptCount() <= self::MAX_ATTEMPTS || self::UNLIMITED_ATTEMPTS) {
                                $this->sms->resendSMS($smsNotification);
                            } else {
                                $smsNotification->setStatus($this->smsStatuses['not_delivered']);
                                $this->logger
                                    ->setUser($systemUser)
                                    ->setDescription(
                                        $this->translator->trans(
                                            'message.fail.provider.error',
                                            ['%id%' => $smsNotification->getId()]
                                        )
                                    )
                                    ->logFailEvent();
                                $em->persist($smsNotification);
                            }
                            break;
                        case $this->smsStatuses['failed']:
                            $smsNotification->setStatus($this->smsStatuses['failed']);
                            $this->logger
                                ->setUser($systemUser)
                                ->setDescription(
                                    $this->translator->trans(
                                        'message.fail.wrong.number',
                                        ['%id%' => $smsNotification->getId()]
                                    )
                                )
                                ->logFailEvent();
                            $em->persist($smsNotification);
                            break;
                    }
                }
            }
        }
        $this->logger
            ->setUser($systemUser)
            ->setDescription($this->translator->trans(
                'command.success',
                ['%command%' => self::$defaultName]
            ))
            ->logSuccessEvent();

        $em->flush();

        return Command::SUCCESS;
    }
}