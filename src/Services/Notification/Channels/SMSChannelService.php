<?php

namespace App\Services\Notification\Channels;

use App\Entity\AuthUser;
use App\Entity\ChannelType;
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
 * Сервис отправки СМС сообщений
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class SMSChannelService
{
    /** Константа типа канала  */
    const SMS_CHANNEL = 'sms-beeline';

    /** Константа sms провайдера  */
    const SMS_PROVIDER_BEELINE = 'Beeline';

    /** @var EntityManagerInterface Энтити менеджер */
    private $em;

    /** @var string Текст СМС сообщения */
    private $text;

    /** @var AuthUser Пользователь получатель */
    private $authUser;

    /**
     * @var array Параметры для работы с смс провайдером
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $SMS_PARAMETERS;

    /**
     * @var array Стандартизированные статусы СМС сообщений
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $SMS_STATUSES;

    /**
     * @var array Стандартизированные временные диапазоны обновления и получения SMS уведомлений
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $SMS_UPDATE_TIMES;

    /**
     * @var array Стандартизированные параметры телефонных номеров
     * yaml:config/services/notifications/sms_notification_service.yml
     */
    private $PHONE_PARAMETERS;

    /**
     * @var array Стандартизированные форматы времени
     * yaml:config/globals.yaml
     */
    private $TIME_FORMATS;

    /**
     * @var string Телефон системного пользователя
     * yaml:config/globals.yaml
     */
    private $SYSTEM_USER_PHONE;

    /** @var LogService Сервис логирования */
    private $logger;

    /** @var BeelineSMSProvider Сервис для работы с BeelineSMS провайдером */
    private $beelineSMSProvider;

    /** @var TranslatorInterface Интерфейс для работы с переводом */
    private $translator;

    /** @var DateTime Дата и время отправки уведомления */
    private $notificationTime;

    /** @var AuthUser Пользователь отправитель */
    private $userSender;

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
    )
    {
        $this->em = $em;
        $this->logger = $logService;
        $this->beelineSMSProvider = $beelineSMSProvider;
        $this->translator = $translator;
        $this->SMS_PARAMETERS = $smsParameters;
        $this->SMS_STATUSES = $smsStatuses;
        $this->SMS_UPDATE_TIMES = $smsUpdateTimes;
        $this->PHONE_PARAMETERS = $phoneParameters;
        $this->TIME_FORMATS = $timeFormats;
        $this->SYSTEM_USER_PHONE = $systemUserPhone;
        $this->notificationTime = new DateTime('now');
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->SYSTEM_USER_PHONE]);
    }

    /**
     * SMS Sender and result parser
     * @return mixed
     */
    public function sendSMS(): SMSNotification
    {
        $result = $this->sendBeelineSMS();
        // TODO: Расширить, если появится второй провайдер

        $sMSNotification = new SMSNotification();
        $sMSNotification->setRecipientPhone($this->authUser->getPhone());
        $sMSNotification->setStatus($this->SMS_STATUSES['wait']);
        $sMSNotification->setExternalId((string)$result->result->sms['id']);
        $sMSNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName(self::SMS_CHANNEL)
        );

        $this->em->persist($sMSNotification);
        $this->logger
            ->setUser(
                $userSender
                ?? $this->em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->SYSTEM_USER_PHONE])
            )
            ->setDescription(
                $this->translator->trans(
                    'log.new.entity', ['%entity%' => 'SMS Уведомление', '%id%' => $sMSNotification->getId()])
            )
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
                    $this->PHONE_PARAMETERS['phone_prefix_ru'] . $this->authUser->getPhone()
                )
                ->send()
        );
    }

    /**
     * SMS RE-Sender and result parser
     * @param SMSNotification $SMSNotification
     * @return bool
     */
    public function reSendSMS(SMSNotification $SMSNotification): bool
    {
        $result = new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setText($SMSNotification->getNotification()->getText())
                ->setTarget(
                    $this->PHONE_PARAMETERS['phone_prefix_ru'] .
                    $SMSNotification->getNotification()->getPatientNotification()
                        ->getPatient()->getAuthUser()->getPhone()
                )
                ->send()
        );
        $currentAttemptCount = (int)$SMSNotification->getAttemptCount();
        $SMSNotification->setExternalId((string)$result->result->sms['id']);
        $SMSNotification->setAttemptCount(++$currentAttemptCount);
        $this->em->persist($SMSNotification);
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
        return (
        new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setDateTimeEnd($this->notificationTime->format($this->TIME_FORMATS['besms']))
                ->setDateTimeStart($this->notificationTime
                    ->sub(new DateInterval('PT' . $this->SMS_UPDATE_TIMES['period_to_update'] . 'H'))
                    ->format($this->TIME_FORMATS['besms']))
                ->check()
        )
        )->MESSAGES->MESSAGE;
    }

    /**
     * SMS Getter and result parser
     * @return mixed
     * @throws Exception
     */
    public function getUnreadSMS()
    {
        return (
        new SimpleXMLElement(
            $this->beelineSMSProvider
                ->setDateTimeEnd($this->notificationTime->format($this->TIME_FORMATS['besms']))
                ->setDateTimeStart($this->notificationTime
                    ->sub(new DateInterval('PT' . $this->SMS_UPDATE_TIMES['period_to_check'] . 'H'))
                    ->format($this->TIME_FORMATS['besms']))
                ->getMessages()
        )
        )->MESSAGES->MESSAGE;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param AuthUser $authUser
     * @return $this
     */
    public function setAuthUser(AuthUser $authUser): self
    {
        $this->authUser = $authUser;
        return $this;
    }
}