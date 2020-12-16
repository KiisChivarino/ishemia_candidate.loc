<?php

namespace App\Services\Notification;

use App\API\BEESMS;
use App\Entity\Patient;
use App\Entity\SMSNotification;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class SMSNotificationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
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

    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     * @param array $smsParameters
     * @param array $smsStatuses
     * @param array $smsUpdateTimes
     * @param array $phoneParameters
     * @param array $timeFormats
     */
    public function __construct(
        EntityManagerInterface $em,
        array $smsParameters,
        array $smsStatuses,
        array $smsUpdateTimes,
        array $phoneParameters,
        array $timeFormats
    ) {
        $this->em = $em;
        $this->smsParameters = $smsParameters;
        $this->smsStatuses = $smsStatuses;
        $this->smsUpdateTimes = $smsUpdateTimes;
        $this->phoneParameters = $phoneParameters;
        $this->timeFormats = $timeFormats;
        $this->sms = new BEESMS($this->smsParameters['user'], $this->smsParameters['password']);
    }

    /**
     * Send SMS
     * @param string $text
     * @param string $target
     * @return false|string
     */
    private function send(string $text, string $target): string
    {
        return $this->sms->post_message($text, $target, $this->smsParameters['sender']);
    }

    /**
     * Check SMS form server
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function check(string $dateFrom, string $dateTo): string
    {
        return $this->sms->status_sms_date($dateFrom, $dateTo);
    }

    /**
     * Get SMS form inbox
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function getMessages(string $dateFrom, string $dateTo): string
    {
        return $this->sms->status_inbox(false,0,$dateFrom,$dateTo);
    }

    /**
     * SMS Sender and result parser
     * @return SMSNotification
     */
    public function sendSMS(): SMSNotification
    {
        $result = new SimpleXMLElement(
            $this->send(
                $this->text,
                $this->phoneParameters['phone_prefix_ru'] . $this->patient->getAuthUser()->getPhone()
            )
        );
        $sMSNotification = new SMSNotification();
        $sMSNotification->setSmsTo($this->patient->getAuthUser()->getPhone());
        $sMSNotification->setStatus($this->smsStatuses['wait']);
        $sMSNotification->setExternalId((string)$result->result->sms['id']);

        $this->em->persist($sMSNotification);
        $this->em->flush();

        return $sMSNotification;
    }

    /**
     * SMS RE-Sender and result parser
     * @param SMSNotification $sMSNotification
     * @return bool
     */
    public function reSendSMS(SMSNotification $sMSNotification): bool
    {
        $result = new SimpleXMLElement(
            $this->send(
                $sMSNotification->getNotification()->getText(),
                $this->phoneParameters['phone_prefix_ru'] .
                    $sMSNotification->getNotification()->getPatient()->getAuthUser()->getPhone()
            )
        );
        $sMSNotification->setExternalId((string)$result->result->sms['id']);
        $sMSNotification->setAttempt((int)$sMSNotification->getAttempt() + 1);

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
        return new SimpleXMLElement($this->check(
            (new DateTime('now'))
                ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_update'] . 'H'))
                ->format($this->timeFormats['besms']),
            (new DateTime('now'))->format($this->timeFormats['besms'])
        ));
    }

    /**
     * SMS Getter and result parser
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function getUnreadSMS(): SimpleXMLElement
    {
        return new SimpleXMLElement($this->getMessages(
            (new DateTime('now'))
                ->sub(new DateInterval('PT' . $this->smsUpdateTimes['period_to_check'] . 'H'))
                ->format($this->timeFormats['besms']),
            (new DateTime('now'))->format($this->timeFormats['besms'])
        ));
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
}