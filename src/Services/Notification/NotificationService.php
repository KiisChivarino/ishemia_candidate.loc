<?php

namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\Notification;
use App\Entity\NotificationConfirm;
use App\Entity\NotificationReceiverType;
use App\Entity\NotificationTemplate;
use App\Entity\NotificationTemplateText;
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
    /** @var EntityManagerInterface Энтити менеджер */
    protected $em;

    /** @var AuthUser Сущность пользователя (отправитель уведомления) */
    protected $userSender;

    /** @var LogService Сервис логирования */
    protected $logger;

    /** @var TranslatorInterface Интерфейс для работы с переводом */
    protected $translator;

    /**
     * @var array
     * yaml:config/services/notifications/notification_channel_types.yaml
     */
    protected $CHANNEL_TYPES;

    /** @var string */
    protected $channelType;

    /** @var NotificationData */
    protected $notificationData;

    /** @var array Строки для добавления конкретной информации в стандартизированные шаблоны */
    private $variables;

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
     * @param array $channelTypes
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        array $channelTypes
    )
    {
        $this->em = $em;
        $this->logger = $logService;
        $this->translator = $translator;
        $this->CHANNEL_TYPES = $channelTypes;
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->getSystemUser();
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

    /**
     * @return NotificationData
     */
    public function getNotificationData(): NotificationData
    {
        return $this->notificationData;
    }

    /**
     * @param NotificationData $notificationData
     * @return NotificationService
     */
    public function setNotificationData(NotificationData $notificationData): self
    {
        $this->notificationData = $notificationData;
        return $this;
    }

    public function notify()
    {
        // TODO: Implement notify() method.
    }

    /**
     * Creates new Notification
     * @return Notification
     */
    protected function createNotification(): Notification
    {
        $notification = new Notification();
        $notification->setPatientNotification($this->createPatientNotification());
        $notification->setText($this->getNotificationText());
        $notification->setAuthUserSender($this->userSender);
        $notification->setNotificationReceiverType($this->notificationReceiverType);
        $notification->setNotificationTime(new DateTime('now'));
        $notification->setNotificationTemplate($this->notificationTemplate);
        return $notification;
    }

    /**
     * Creates new PatientNotification
     * @return PatientNotification
     */
    private function createPatientNotification(): PatientNotification
    {
        $patientNotification = (new PatientNotification())
            ->setMedicalRecord($this->notificationData->getMedicalRecord())
            ->setMedicalHistory($this->notificationData->getMedicalHistory() ?? null)
            ->setPatient($this->notificationData->getPatientReceiver())
            ->setNotificationConfirm($this->notificationConfirm);
        $this->em->persist($patientNotification);
        return $patientNotification;
    }

    /**
     * Generates text for specific channel
     * @return string
     */
    private function getNotificationText(): string
    {
        return vsprintf(
            $this->em->getRepository(NotificationTemplateText::class)->findForChannel(
                $this->channelType, $this->notificationTemplate
            )->getText(),
            $this->variables
        );
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
}