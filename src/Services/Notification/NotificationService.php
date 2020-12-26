<?php

namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\ChannelType;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Entity\NotificationConfirm;
use App\Entity\NotificationReceiverType;
use App\Entity\NotificationTemplate;
use App\Entity\NotificationTemplateText;
use App\Entity\Patient;
use App\Entity\PatientNotification;
use App\Services\LoggerService\LogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Сервис отправки уведомлений
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
abstract class NotificationService implements NotificationInterface
{
    /** Константы для типов каналов  */
    const
        EMAIL_CHANNEL = 'email',
        SMS_CHANNEL = 'sms-beeline',
        WEB_CHANNEL = 'web'
    ;

    /** Константы для типов получателей  */
    const
        RECEIVER_TYPE_PATIENT = 'patient'
    ;

    /** Константы для sms провайдеров  */
    const
        SMS_PROVIDER_BEELINE = 'Beeline'
    ;

    /** @var EntityManagerInterface Энтити менеджер */
    protected $em;

    /** @var array Строки для добавления конкретной информации в стандартизированные шаблоны */
    private $variables;

    /** @var Patient Сущность пациента */
    protected $patientReceiver;

    /** @var AuthUser Сущность пользователя (отправитель уведомления) */
    protected $userSender;

    /** @var LogService Сервис логирования */
    protected $logger;

    /** @var string Телефон системного пользователя */
    private $systemUserPhone;

    /** @var MedicalHistory История болезни пациента */
    private $medicalHistory;

    /** @var MedicalRecord Запись в истории болезни пациента */
    private $medicalRecord;

    /** @var TranslatorInterface Интерфейс для работы с переводом */
    protected $translator;

    /** @var NotificationTemplate Шаблон уведомления */
    private $notificationTemplate;

    /** @var NotificationReceiverType Тип получателя уведомления */
    private $notificationReceiverType;

    /** @var NotificationConfirm */
    private $notificationConfirm;

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        string $systemUserPhone
    ) {
        $this->em = $em;
        $this->logger = $logService;
        $this->translator = $translator;
        $this->systemUserPhone = $systemUserPhone;
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->findOneBy(['phone'=>$this->systemUserPhone]);
    }

    /**
     * Creates new Notification
     * @param string $channel
     * @return Notification
     */
    protected function createNotification(string $channel): Notification
    {
        $notification = new Notification();
        $notification->setPatientNotification($this->createPatientNotification());
        $notification->setText($this->getNotificationText($channel));
        $notification->setAuthUserSender($this->userSender);
        $notification->setNotificationReceiverType($this->notificationReceiverType);
        $notification->setNotificationTime(new DateTime('now'));
        $notification->setNotificationTemplate($this->notificationTemplate);
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findByName($channel)
        );
        return $notification;
    }

    /**
     * Generates text for specific channel
     * @param $channel
     * @return string
     */
    private function getNotificationText(string $channel): string
    {
        return vsprintf(
            $this->em->getRepository(NotificationTemplateText::class)->findForChannel(
                $channel, $this->notificationTemplate
            )->getText(),
            $this->variables
        );
    }

    /**
     * Creates new PatientNotification
     * @return PatientNotification
     */
    private function createPatientNotification(): PatientNotification
    {
        $patientNotification = (new PatientNotification())
            ->setMedicalRecord($this->medicalRecord ?? null)
            ->setMedicalHistory($this->medicalHistory ?? null)
            ->setPatient($this->patientReceiver)
            ->setNotificationConfirm($this->notificationConfirm)
        ;
        $this->em->persist($patientNotification);
        return $patientNotification;
    }

    /**
     * Creates log for successful notification creation
     * @param Notification $notification
     * @return bool
     */
    protected function logSuccessNotificationCreation(Notification $notification): bool
    {
        $this->logger
            ->setUser($this->userSender)
            ->setDescription(
                $this->translator->trans(
                    'log.new.entity',
                    [
                        '%entity%' => 'Уведомление',
                        '%id%' => $notification->getId()
                    ]
                )
            )
            ->logSuccessEvent();
        return true;
    }

//  ---------------------------------------- Сеттеры ----------------------------------------------------------

    /**
     * @param Patient $patient
     * @return NotificationService
     */
    public function setPatient(Patient $patient): self
    {
        $this->patientReceiver = $patient;
        return $this;
    }

    public function getPatient(): Patient
    {
        return $this->patientReceiver;
    }

    /**
     * @param array $variables
     * @return NotificationService
     */
    public function setVariables(array $variables): self
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return NotificationService
     */
    public function setMedicalHistory(MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }

    /**
     * @param MedicalRecord $medicalRecord
     * @return NotificationService
     */
    public function setMedicalRecord(MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * @param string $notificationReceiverType
     * @return NotificationService
     */
    public function setNotificationReceiverType(string $notificationReceiverType): self
    {
        $this->notificationReceiverType = $this->em
            ->getRepository(NotificationReceiverType::class)
            ->findByName($notificationReceiverType);
        return $this;
    }

    /**
     * @param string $notificationTemplate
     * @return NotificationService
     */
    public function setNotificationTemplate(string $notificationTemplate): self
    {
        $this->notificationTemplate = $this->em
            ->getRepository(NotificationTemplate::class)
            ->findByName($notificationTemplate);
        return $this;
    }

    /**
     * @param NotificationConfirm $notificationConfirm
     * @return NotificationService
     */
    public function setNotificationConfirm(NotificationConfirm $notificationConfirm): self
    {
        $this->notificationConfirm = $notificationConfirm;
        return $this;
    }

    public function notify()
    {
        // TODO: Implement notify() method.
    }
}