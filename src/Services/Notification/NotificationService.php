<?php


namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\ChannelType;
use App\Entity\EmailNotification;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Entity\NotificationReceiverType;
use App\Entity\NotificationTemplate;
use App\Entity\NotificationTemplateText;
use App\Entity\Patient;
use App\Entity\PatientNotification;
use App\Entity\WebNotification;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\LoggerService\LogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class NotificationService
{
    /** Константы для типов каналов  */
    const
        EMAIL_CHANNEL = 'email',
        SMS_CHANNEL = 'sms',
        WEB_CHANNEL = 'web'
    ;

    /** @var EntityManagerInterface */
    private $em;

    /** @var array */
    private $texts;

    /** @var SMSNotificationService */
    private $sms;

    /** @var Patient */
    private $patient;

    /** @var EmailNotificationService */
    private $email;

    /** @var AuthUser */
    private $user;

    /** @var LogService */
    private $logger;

    /** @var string */
    private $systemUserPhone;

    /** @var MedicalHistory */
    private $medicalHistory;

    /** @var MedicalRecord */
    private $medicalRecord;

    /** @var TranslatorInterface */
    private $translator;

    /** @var NotificationTemplate */
    private $notificationTemplate;

    /** @var NotificationReceiverType */
    private $notificationReceiverType;

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param SMSNotificationService $sMSNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param TranslatorInterface $translator
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $em,
        SMSNotificationService $sMSNotificationService,
        EmailNotificationService $emailNotificationService,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        TranslatorInterface $translator,
        string $systemUserPhone
    ) {
        $this->em = $em;
        $this->sms = $sMSNotificationService;
        $this->email = $emailNotificationService;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $logService;
        $this->translator = $translator;
        $this->systemUserPhone = $systemUserPhone;
    }

    /**
     * Creates new WebNotification
     * @param string $channel
     * @return Notification
     */
    private function createNotification(string $channel): Notification
    {
        $notification = new Notification();
        $notification->setPatientNotification($this->createPatientNotification());
        $notification->setText(
            vsprintf(
                $this->em->getRepository(NotificationTemplateText::class)->findForChannel(
                    $channel, $this->notificationTemplate
                )->getText(),
                $this->texts
            )
        );
        $notification->setAuthUserSender($this->user);
        $notification->setNotificationReceiverType($this->notificationReceiverType);
        $notification->setNotificationTime(new DateTime('now'));
        $notification->setNotificationTemplate($this->notificationTemplate);
        $notification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findOneBy(['name' => $channel])
        );
        return $notification;
    }

    /**
     * Send SMS notification
     * @return bool
     */
    private function notifyUserViaSMS(): bool
    {
        $notification = $this->createNotification(self::SMS_CHANNEL);
        $notification
            ->setSmsNotification(
                $this->sms
                    ->setText($notification->getText())
                    ->setPatient($this->patient)
                    ->setProvider('Beeline')
                    ->sendSMS()
        );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }

    /**
     * Send Email notification
     * @return EmailNotification
     */
    private function notifyUserViaEmail(): EmailNotification
    {
        $notification = $this->createNotification(self::EMAIL_CHANNEL);
        $emailNotification = new EmailNotification();
        $emailNotification->setPatientRecipientEmail($this->patient->getAuthUser()->getEmail());
        $emailNotification->setChannelType(
            $this->em->getRepository(ChannelType::class)->findOneBy(['name' => self::EMAIL_CHANNEL])
        );

        try {
            $this->email
                ->setPatient($this->patient)
                ->setHeader('Добрый день!')
                ->setContent($notification->getText())
                ->sendDefaultEmail();
            $this->em->persist($emailNotification);
            $this->logger
                ->setUser($this->user)
                ->setDescription(
                    $this->translator->trans(
                        'log.new.entity',
                        ['%entity%' => 'Email уведомление', '%id%' => $emailNotification->getId()]
                    )
                )
                ->logSuccessEvent();
        } catch (ErrorException | LoaderError | RuntimeError | SyntaxError $e) {
            $this->logger
                ->setUser($this->user)
                ->setDescription($e)
                ->logErrorEvent();
        }


        $notification->setEmailNotification($emailNotification);
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);

        return $emailNotification;
    }

    /**
     * Notify user via Web channel
     * @return bool
     */
    private function notifyUserViaWeb(): bool
    {
        $notification = $this->createNotification(self::WEB_CHANNEL)->setWebNotification(
            $this->createWebNotification()
        );
        $this->em->persist($notification);
        $this->logSuccessNotificationCreation($notification);
        return true;
    }

    /**
     * Creates new WebNotification
     * @return WebNotification
     */
    private function createWebNotification(): WebNotification
    {
        $webNotification = (new WebNotification())
            ->setReceiverString((new AuthUserInfoService())->getFIO($this->patient->getAuthUser()))
            ->setChannelType(
                $this->em->getRepository(ChannelType::class)->findOneBy(['name' => self::WEB_CHANNEL])
            );
        ;
        $this->em->persist($webNotification);
        return $webNotification;
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
            ->setPatient($this->patient);
        $this->em->persist($patientNotification);
        return $patientNotification;
    }

    /**
     * Creates log for successful notification creation
     * @param Notification $notification
     * @return bool
     */
    private function logSuccessNotificationCreation(Notification $notification): bool
    {
        $this->logger
            ->setUser($this->user)
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

    /**
     * Notification sender
     * @return void
     */
    public function notifyPatient(): void
    {
        $this->notifyUserViaWeb();

        if ($this->patient->getSmsInforming()) {
            $this->notifyUserViaSMS();
        }
        if ($this->patient->getEmailInforming()) {
            $this->notifyUserViaEmail();
        }
        $this->em->flush();
    }

//  ---------------------------------------- Сеттеры ----------------------------------------------------------

    /**
     * @param Patient $patient
     * @return NotificationService
     */
    public function setPatient(Patient $patient): NotificationService
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * @param array $texts
     * @return NotificationService
     */
    public function setTexts(array $texts): NotificationService
    {
        $this->texts = $texts;
        return $this;
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return NotificationService
     */
    public function setMedicalHistory(MedicalHistory $medicalHistory): NotificationService
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }

    /**
     * @param MedicalRecord $medicalRecord
     * @return NotificationService
     */
    public function setMedicalRecord(MedicalRecord $medicalRecord): NotificationService
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * @param string $notificationReceiverType
     * @return NotificationService
     */
    public function setNotificationReceiverType(string $notificationReceiverType): NotificationService
    {
        $this->notificationReceiverType = $this->em->getRepository(NotificationReceiverType::class)->findOneBy(
            [
                'name' => $notificationReceiverType
            ]
        );
        return $this;
    }

    /**
     * @param string $notificationTemplate
     * @return NotificationService
     */
    public function setNotificationTemplate(string $notificationTemplate): NotificationService
    {
        $this->notificationTemplate = $this->em->getRepository(NotificationTemplate::class)->findOneBy(
            [
                'name' => $notificationTemplate
            ]
        );
        return $this;
    }
}