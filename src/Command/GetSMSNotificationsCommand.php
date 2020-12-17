<?php

namespace App\Command;

use App\Entity\Patient;
use App\Entity\PatientSMS;
use App\Services\Notification\SMSNotificationService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    /** @var SMSNotificationService */
    private $sms;

    /** @var array */
    private $smsParameters;

    /** @var array */
    private $phoneParameters;

    /**
     * GetSMSNotificationsCommand constructor.
     * @param ContainerInterface $container
     * @param SMSNotificationService $SMSNotificationService
     * @param array $smsParameters
     * @param array $phoneParameters
     */
    public function __construct(
        ContainerInterface $container,
        SMSNotificationService $SMSNotificationService,
        array $smsParameters,
        array $phoneParameters
    ) {
        parent::__construct();
        $this->container = $container;
        $this->sms = $SMSNotificationService;
        $this->smsParameters = $smsParameters;
        $this->phoneParameters = $phoneParameters;
    }

    /**
     * Конфигурация для команды GetSMSNotifications
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Get SMS notification`s')
            ->setHelp('This command gets SMS notification`s')
        ;
    }

    /**
     * Gets sms for the past hour, and puts into DB
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->container->get('doctrine')->getManager();
        $patients = $em->getRepository(Patient::class)->findAll();
        $smsCollection = $em->getRepository(PatientSMS::class)->findAll();

        $result = $this->sms->getUnreadSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            if ((string) $message->SMS_TARGET == $this->smsParameters['sender']) {
                foreach ($patients as $patient) {
                    if (
                        (string) $message->SMS_SENDER == (string) $this->phoneParameters['phone_prefix_ru'] .
                        $patient->getAuthUser()->getPhone()
                    ) {
                        $check = false;
                        foreach ($smsCollection as $sms) {
                            if ($sms->getExternalId() == (string) $message['SMS_ID']) {
                                $check = true;
                            }
                        }
                        if (!$check) {
                            $sms = new PatientSMS();
                            $sms->setPatient($patient);
                            $sms->setText((string) $message->SMS_TEXT);
                            $sms->setExternalId((string) $message['SMS_ID']);
                            $sms->setCreatedAt(
                                date_create_from_format('d.m.y H:i:s', (string) $message->SMS_CLOSE_TIME)
                            );
                            $em->persist($sms);
                        }
                    }
                }
            }
        }
        $em->flush();
        return Command::SUCCESS; // TODO: При ошибки лог с ошибкой добавить
    }
}