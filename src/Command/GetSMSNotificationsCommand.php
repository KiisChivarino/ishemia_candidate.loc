<?php

namespace App\Command;

use App\Entity\Patient;
use App\Entity\ReceivedSMS;
use App\Services\Notification\SMSNotificationService;
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

    /** @var string prefix for RU phone numbers */
    const PHONE_PREFIX_RU = '+7';

    /** @var string Auth data for sms service */
    const
        SENDER = '3303',
        SMS_USER = '775000',
        SMS_PASSWORD = 'Yandex10241024'
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
     * GetSMSNotificationsCommand constructor.
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
            ->setDescription('Get SMS notification`s')
            ->setHelp('This command gets SMS notification`s')
        ;
    }

    /**
     * Gets sms for the past hour, and puts into DB
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->container->get('doctrine')->getManager();
        $patients = $em->getRepository(Patient::class)->findAll();
        $smses = $em->getRepository(ReceivedSMS::class)->findAll();

        $result = $this->sms->getUnreadSMS();
        foreach ($result->MESSAGES->MESSAGE as $message) {
            if ((string) $message->SMS_TARGET == self::SENDER) {
                foreach ($patients as $patient) {
                    if ((string) $message->SMS_SENDER == (string) self::PHONE_PREFIX_RU . $patient->getAuthUser()->getPhone()) {
                        $check = false;
                        foreach ($smses as $sms) {
                            if ($sms->getExternalId() == (string) $message['SMS_ID']) {
                                $check = true;
                            }
                        }
                        if (!$check) {
                            $sms = new ReceivedSMS();
                            $sms->setPatient($patient);
                            $sms->setText((string) $message->SMS_TEXT);
                            $sms->setExternalId((string) $message['SMS_ID']);
                            $sms->setCreatedAt(date_create_from_format('d.m.y H:i:s', (string) $message->SMS_CLOSE_TIME));
                            $em->persist($sms);
                        }
                    }
                }
            }
        }
        $em->flush();
        return 0;
    }
}