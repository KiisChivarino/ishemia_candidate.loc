<?php

namespace App\Services\Notification;

use App\API\BEESMS;
use App\Entity\ChannelType;
use App\Entity\Patient;
use App\Entity\SMSNotification;
use App\Services\LoggerService\LogService;
use App\Services\SMSProviders\BeelineSMSProvider;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class SMSNotificationService
{
    /** Константы для типов каналов  */
    const
        SMS_CHANNEL = 'sms'
    ;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $text;

    /** @var Patient */
    private $patient;

    /** @var array */
    private $smsParameters;

    /** @var BEESMS */
    private $sms;

    /** @var array */
    private $smsStatuses;

    /** @var array */
    private $phoneParameters;

    /** @var array */
    private $smsUpdateTimes;

    /** @var array */
    private $timeFormats;

    /** @var string */
    private $systemUserPhone;

    /** @var LogService */
    private $logger;

    /** @var BeelineSMSProvider */
    private $beelineSMSProvider;

    /** @var string */
    private $provider;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param LogService $logService
     * @param TokenStorageInterface $tokenStorage
     * @param BeelineSMSProvider $beelineSMSProvider
     * @param TranslatorInterface $translator
     * @param array $smsParameters
     * @param array $smsStatuses
     * @param array $smsUpdateTimes
     * @param array $phoneParameters
     * @param array $timeFormats
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $em,
        LogService $logService,
        TokenStorageInterface $tokenStorage,
        BeelineSMSProvider $beelineSMSProvider,
        TranslatorInterface $translator,
        array $smsParameters,
        array $smsStatuses,
        array $smsUpdateTimes,
        array $phoneParameters,
        array $timeFormats,
        string $systemUserPhone
    ) {
        $this->em = $em;
        $this->logger = $logService;
        $this->tokenStorage = $tokenStorage;
        $this->beelineSMSProvider = $beelineSMSProvider;
        $this->translator = $translator;
        $this->smsParameters = $smsParameters;
        $this->smsStatuses = $smsStatuses;
        $this->smsUpdateTimes = $smsUpdateTimes;
        $this->phoneParameters = $phoneParameters;
        $this->timeFormats = $timeFormats;
        $this->systemUserPhone = $systemUserPhone;
    }

    /**
     * SMS Sender and result parser
     * @return mixed
     */
    public function sendSMS()
    {
        $result = null;
        switch ($this->provider) {
            case 'Beeline':
                $result = $this->sendBeelineSMS();
                break;
        }
        if (is_null($result)) {
            return false;
        }
        $sMSNotification = new SMSNotification();
        $sMSNotification->setSmsPatientRecipientPhone($this->patient->getAuthUser()->getPhone());
        $sMSNotification->setStatus($this->smsStatuses['wait']);
        $sMSNotification->setExternalId((string)$result->result->sms['id']);
        $sMSNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findOneBy(['name' => self::SMS_CHANNEL])
        );

        $this->em->persist($sMSNotification);
        $this->logger
            ->setUser($this->tokenStorage->getToken()->getUser())
            ->setDescription($this->translator->trans('log.new.entity', ['%entity%' => 'SMS Уведомление', '%id%' => $sMSNotification->getId()]))
            ->logSuccessEvent();

        return $sMSNotification;
    }

    /**
     * SMS Sender and result parser
     * @return SimpleXMLElement
     */
    public function sendBeelineSMS(): SimpleXMLElement
    {
        return new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setText($this->text)
                ->setTarget(
                    $this->phoneParameters['phone_prefix_ru'] . $this->patient->getAuthUser()->getPhone()
                )
                ->send()
        );
    }

    /**
     * SMS RE-Sender and result parser
     * @param SMSNotification $sMSNotification
     * @return bool
     */
    public function reSendSMS(SMSNotification $sMSNotification): bool
    {
        $result = new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setText($sMSNotification->getNotification()->getText())
                ->setTarget(
                    $this->phoneParameters['phone_prefix_ru'] .
                        $sMSNotification->getNotification()->getPatient()->getAuthUser()->getPhone()
                )
                ->send()
        );
        $sMSNotification->setExternalId((string)$result->result->sms['id']);
        $sMSNotification->setAttemptCount((int)$sMSNotification->getAttemptCount() + 1);

        $this->em->persist($sMSNotification);
        $this->em->flush();
        return true;
    }

    /**
     * SMS Checker and result parser
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function checkSMS(): SimpleXMLElement
    {
        return new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setDateTimeStart((new DateTime('now'))
                    ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_update'] . 'H'))
                    ->format($this->timeFormats['besms']))
                ->setDateTimeEnd((new DateTime('now'))->format($this->timeFormats['besms']))
                ->check()
        );
    }

    /**
     * SMS Getter and result parser
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function getUnreadSMS(): SimpleXMLElement
    {
        return new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setDateTimeStart((new DateTime('now'))
                    ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_check'] . 'H'))
                    ->format($this->timeFormats['besms']))
                ->setDateTimeEnd((new DateTime('now'))->format($this->timeFormats['besms']))
                ->getMessages()
        );
    }

    /**
     * @param string $text
     * @return SMSNotificationService
     */
    public function setText(string $text): SMSNotificationService
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param Patient $patient
     * @return $this
     */
    public function setPatient(Patient $patient): SMSNotificationService
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * @param string $provider
     * @return $this
     */
    public function setProvider(string $provider): SMSNotificationService
    {
        $this->provider = $provider;
        return $this;
    }
}