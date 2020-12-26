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
    /** Константы для типов каналов  */
    const
        SMS_CHANNEL = 'sms-beeline'
    ;

    /** Константы для sms провайдеров  */
    const
        SMS_PROVIDER_BEELINE = 'Beeline'
    ;

    /** @var EntityManagerInterface Энтити менеджер */
    private $em;

    /** @var string Текст СМС сообщения */
    private $text;

    /** @var AuthUser Пользователь получатель*/
    private $authUser;

    /** @var array Параметры для работы с смс провайдером */
    private $smsParameters;

    /** @var array Стандартизированные статусы СМС сообщений */
    private $smsStatuses;

    /** @var array Стандартизированные параметры телефонных номеров */
    private $phoneParameters;

    /** @var array Стандартизированные временные диапазоны обновления и получения SMS уведомлений */
    private $smsUpdateTimes;

    /** @var array Стандартизированные форматы времени */
    private $timeFormats;

    /** @var string Телефон системного пользователя */
    private $systemUserPhone;

    /** @var LogService Сервис логирования */
    private $logger;

    /** @var BeelineSMSProvider Сервис для работы с BeelineSMS провайдером */
    private $beelineSMSProvider;

    /** @var string Название провайдера предоставления услуг */
    private $provider;

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
    ) {
        $this->em = $em;
        $this->logger = $logService;
        $this->beelineSMSProvider = $beelineSMSProvider;
        $this->translator = $translator;
        $this->smsParameters = $smsParameters;
        $this->smsStatuses = $smsStatuses;
        $this->smsUpdateTimes = $smsUpdateTimes;
        $this->phoneParameters = $phoneParameters;
        $this->timeFormats = $timeFormats;
        $this->systemUserPhone = $systemUserPhone;
        $this->notificationTime = new DateTime('now');
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->findOneBy(['phone'=>$this->systemUserPhone]);
    }

    /**
     * SMS Sender and result parser
     * @return mixed
     */
    public function sendSMS()
    {
        $result = null;
        switch ($this->provider) {
            case self::SMS_PROVIDER_BEELINE:
                $result = $this->sendBeelineSMS();
                break;
        }
        if (is_null($result)) {
            return false;
        }
        $sMSNotification = new SMSNotification();
        $sMSNotification->setRecipientPhone($this->authUser->getPhone());
        $sMSNotification->setStatus($this->smsStatuses['wait']);
        $sMSNotification->setExternalId((string)$result->result->sms['id']);
        $sMSNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName(self::SMS_CHANNEL)
        );

        $this->em->persist($sMSNotification);
        $this->logger
            ->setUser(
                $userSender
                ?? $this->em->getRepository(AuthUser::class)->findOneBy(['phone'=>$this->systemUserPhone])
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
                    $this->phoneParameters['phone_prefix_ru'] . $this->authUser->getPhone()
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
                        $sMSNotification->getNotification()->getPatientNotification()
                            ->getPatient()->getAuthUser()->getPhone()
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
                ->setDateTimeEnd($this->notificationTime->format($this->timeFormats['besms']))
                ->setDateTimeStart($this->notificationTime
                    ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_update'] . 'H'))
                    ->format($this->timeFormats['besms']))
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
                ->setDateTimeEnd($this->notificationTime->format($this->timeFormats['besms']))
                ->setDateTimeStart($this->notificationTime
                    ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_check'] . 'H'))
                    ->format($this->timeFormats['besms']))
                ->getMessages()
        );
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

    /**
     * @param string $provider
     * @return $this
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}