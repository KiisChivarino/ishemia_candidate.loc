<?php

namespace App\Command;

use App\Entity\AuthUser;
use App\Entity\NotificationConfirm;
use App\Entity\Patient;
use App\Entity\PatientSMS;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Channels\SMSChannelService;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GetSMSNotificationsCommand
 * @package App\Command
 */
class GetSMSNotificationsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:get-sms';

    /** @var ContainerInterface */
    private $container;

    /** @var SMSChannelService */
    private $smsChannelService;

    /**
     * @var array
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $SMS_CHANNEL_SERVICE_PARAMETERS;

    /**
     * @var array
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $PHONE_PARAMETERS;

    /** @var LogService */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * GetSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSChannelService $SMSChannelService
     * @param LogService $logger
     * @param TranslatorInterface $translator
     * @param array $smsParameters
     * @param array $phoneParameters
     */
    public function __construct(
        ContainerInterface $container,
        SMSChannelService $SMSChannelService,
        LogService $logger,
        TranslatorInterface $translator,
        array $smsParameters,
        array $phoneParameters
    )
    {
        parent::__construct();
        $this->container = $container;
        $this->smsChannelService = $SMSChannelService;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->SMS_CHANNEL_SERVICE_PARAMETERS = $smsParameters;
        $this->PHONE_PARAMETERS = $phoneParameters;
    }

    /**
     * Конфигурация для команды GetSMSNotifications
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Get SMS notification`s')
            ->setHelp('This command gets SMS notification`s');
    }

    /**
     * Gets sms for the past *time interval*, and puts into DB
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->container->get('doctrine')->getManager();
        $patients = $em->getRepository(Patient::class)->findAll();
        $patientSmsCollection = $em->getRepository(PatientSMS::class)->findAll();
        $systemUser = $em->getRepository(AuthUser::class)->getSystemUser();
        $unconfirmedNotifications = $em->getRepository(NotificationConfirm::class)->findBy(
            [
                'isConfirmed' => false
            ]
        );

        /** @var SimpleXMLElement $message */
        foreach ($this->smsChannelService->getUnreadSMS() ?? [] as $message) {
            if ((string)$message->SMS_TARGET == $this->SMS_CHANNEL_SERVICE_PARAMETERS['sender']) {
                foreach ($patients as $patient) {
                    if (
                        (string)$message->SMS_SENDER == (string)$this->PHONE_PARAMETERS['phone_prefix_ru'] .
                        $patient->getAuthUser()->getPhone()
                    ) {
                        $check = false;
                        foreach ($patientSmsCollection as $smsChannelService) {
                            if ($smsChannelService->getExternalId() == (string)$message['SMS_ID']) {
                                $check = true;
                            }
                        }
                        if (!$check) {
                            $smsChannelService = new PatientSMS();
                            $smsChannelService->setPatient($patient);
                            $smsChannelService->setText((string)$message->SMS_TEXT);
                            $smsChannelService->setExternalId((string)$message['SMS_ID']);
                            $smsChannelService->setCreatedAt(
                                date_create_from_format('d.m.y H:i:s', (string)$message->SMS_CLOSE_TIME)
                            );
                            $em->persist($smsChannelService);
                            foreach ($unconfirmedNotifications as $confirm) {
                                if (
                                    (string)$message->SMS_TEXT == $confirm->getSmsCode()
                                    && $this->PHONE_PARAMETERS['phone_prefix_ru'] .
                                    $confirm->getPatientNotification()[0]->getPatient()->getAuthUser()->getPhone()
                                    == (string)$message->SMS_SENDER
                                ) {
                                    $confirm->setIsConfirmed(true);
                                    $em->persist($confirm);
                                }
                            }

                            $this->logger
                                ->setUser($systemUser)
                                ->setDescription(
                                    $this->translator->trans(
                                        'log.new.entity',
                                        ['%entity%' => 'Сообщение пользователя', '%id%' => $smsChannelService->getId()]
                                    )
                                )
                                ->logCreateEvent();
                        }
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

        return Command::SUCCESS; // TODO: При ошибки лог с ошибкой добавить
    }
}