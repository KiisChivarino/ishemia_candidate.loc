<?php

namespace App\Services\Notification;

use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class NotificationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $text;

    /**
     * @var SMSNotificationService
     */
    private $sms;

    /**
     * @var Patient
     */
    private $patient;

    /**
     * @var EmailNotificationService
     */
    private $email;

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param SMSNotificationService $SMSNotificationService
     * @param EmailNotificationService $emailNotificationService
     */
    public function __construct(
        EntityManagerInterface $em,
        SMSNotificationService $SMSNotificationService,
        EmailNotificationService $emailNotificationService
    )
    {
        $this->em = $em;
        $this->sms = $SMSNotificationService;
        $this->email = $emailNotificationService;
    }

    /**
     * Send SMS
     * @param $text
     * @param $target
     * @return false|string
     */
    private function notifyUserViaSMS($text, $target): string
    {
        $this->sms
            ->setText($text)
            ->setTarget($target)
            ->sendSMS();

        return true;
    }

    /**
     * Send SMS
     * @return false|string
     */
    public function notifyUser(): string
    {
        if ($this->patient->getSmsInforming()) {
            $this->notifyUserViaSMS(
                $this->text,
                $this->patient->getAuthUser()->getPhone()
            );
        }

        if ($this->patient->getEmailInforming()) {
            try {
                $this->email
                    ->setPatient($this->patient)
                    ->setHeader('Ура, а вот и вы!')
                    ->setContent($this->text)
                    ->setButtonText('Перейти на сайт')
                    ->setButtonLink('http://shemia.test')
                    ->sendDefaultEmail();
            } catch (ErrorException $e) {
                // TODO: Написать кэтч
            } catch (LoaderError $e) {
                // TODO: Написать кэтч
            } catch (RuntimeError $e) {
                // TODO: Написать кэтч
            } catch (SyntaxError $e) {
                // TODO: Написать кэтч
            }
        }
        return true;
    }

    /**
     * @param Patient $patient
     * @return NotificationService
     */
    public function setPatient(Patient $patient)
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * @param string $text
     * @return NotificationService
     */
    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

}