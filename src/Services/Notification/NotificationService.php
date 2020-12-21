<?php


namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\EmailNotification;
use App\Entity\Notification;
use App\Entity\Patient;
use App\Entity\SMSNotification;
use App\Services\LoggerService\LogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class NotificationService
{
    const DUMMY_NOTIFICATION_TYPE = 'test';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $text;

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

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param SMSNotificationService $sMSNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logService
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $em,
        SMSNotificationService $sMSNotificationService,
        EmailNotificationService $emailNotificationService,
        TokenStorageInterface $tokenStorage,
        LogService $logService,
        string $systemUserPhone
    ) {
        $this->em = $em;
        $this->sms = $sMSNotificationService;
        $this->email = $emailNotificationService;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $logService;
        $this->systemUserPhone = $systemUserPhone;
    }

    /**
     * Send SMS notification
     * @return SMSNotification
     */
    private function notifyUserViaSMS(): SMSNotification
    {
        return $this->sms
            ->setText($this->text)
            ->setPatient($this->patient)
            ->setProvider('Beeline')
            ->sendSMS();
    }

    /**
     * Send Email notification
     * @return EmailNotification
     */
    private function notifyUserViaEmail(): EmailNotification
    {
        $emailNotification = new EmailNotification();
        $emailNotification->setPatientRecipientEmail($this->patient->getAuthUser()->getEmail());

        try {
            $this->email
                ->setPatient($this->patient)
                ->setHeader('Ура, а вот и вы!')
                ->setContent($this->text)
                ->setButtonText('Перейти на сайт')
                ->setButtonLink('http://shemia.test')
                ->sendDefaultEmail();
            $this->em->persist($emailNotification);
            $this->logger
                ->setUser($this->user)
                ->setDescription('Сущность - Email Уведомление (id:'.$emailNotification->getId().') успешно создана.')
                ->logSuccessEvent();
        } catch (ErrorException | LoaderError | RuntimeError | SyntaxError $e) {
            $this->logger
                ->setUser($this->user)
                ->setDescription($e)
                ->logErrorEvent();
        }

        return $emailNotification;
    }

    /**
     * Notification sender
     * @return void
     */
    public function notifyUser(): void
    {
        $notification = new Notification();
        $notification->setPatient($this->patient);
        $notification->setText($this->text);
        $notification->setAuthUserSender($this->user);
        $notification->setNotificationType(self::DUMMY_NOTIFICATION_TYPE);
        $notification->setNotificationTime(new DateTime('now'));

        if ($this->patient->getSmsInforming()) {
            $notification->setSmsNotification(
                $this->notifyUserViaSMS()
            );
        }
        if ($this->patient->getEmailInforming()) {
            $notification->setEmailNotification(
                $this->notifyUserViaEmail()
            );
        }
        $this->em->persist($notification);
        $this->logger
            ->setUser($this->user)
            ->setDescription('Сущность - Уведомление (id:'.$notification->getId().') успешно создана.')
            ->logSuccessEvent();
        $this->em->flush();
    }

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
     * @param string $text
     * @return NotificationService
     */
    public function setText (string $text): NotificationService
    {
        $this->text = $text;
        return $this;
    }

}