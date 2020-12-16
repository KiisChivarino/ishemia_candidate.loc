<?php


namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\EmailNotification;
use App\Entity\Notification;
use App\Entity\Patient;
use App\Entity\SMSNotification;
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

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param SMSNotificationService $sMSNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EntityManagerInterface $em,
        SMSNotificationService $sMSNotificationService,
        EmailNotificationService $emailNotificationService,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->sms = $sMSNotificationService;
        $this->email = $emailNotificationService;
        $this->user = $tokenStorage->getToken()->getUser();
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
            $this->em->flush();
        } catch (ErrorException $e) {
            // TODO: Написать кэтч
        } catch (LoaderError $e) {
            // TODO: Написать кэтч
        } catch (RuntimeError $e) {
            // TODO: Написать кэтч
        } catch (SyntaxError $e) {
            // TODO: Написать кэтч
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
        $notification->setNotificationType('test');
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