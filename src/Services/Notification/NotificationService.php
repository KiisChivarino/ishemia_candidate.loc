<?php

namespace App\Services\Notification;

use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;

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
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param SMSNotificationService $sMSnotificationService
     */
    public function __construct(EntityManagerInterface $em, SMSNotificationService $sMSnotificationService)
    {
        $this->em = $em;
        $this->sms = $sMSnotificationService;
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
            // TODO: Add email notification
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
    public function setText (string $text)
    {
        $this->text = $text;
        return $this;
    }

}