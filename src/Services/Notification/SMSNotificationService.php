<?php


namespace App\Services\Notification;

use App\API\BEESMS;
use App\Entity\AuthUser;
use App\Entity\SMSNotification;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

/**
 * Class SMSNotificationService
 * @package App\Services\Notification
 */
class SMSNotificationService
{
    /** @var int Update time in hours */
    const
        PREIOD_TO_UPDATE_EMAIL = 25,
        PREIOD_TO_CHACK_EMAIL = 2
    ;

    /** @var string Auth data for sms service */
    const
        SENDER = '3303',
        SMS_USER = '775000',
        SMS_PASSWORD = 'Yandex10241024'
    ;

    /** @var string Standard sms statuses */
    const
        DELIVERED = 'delivered', // Статус sms - Доставлено
        NOT_DELIVERED = 'not_delivered', // Статус sms - Не доставлено
        WAIT = 'wait', // Статус sms - Ожидание доставки
        FAILED = 'failed' // Статус sms - Ошибка
    ;

    /** @var string prefix for RU phone numbers */
    const PHONE_PREFIX_RU = '+7';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $target;


    /**
     * SMS notification constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Send SMS
     * @param string $text
     * @param string $target
     * @return false|string
     */
    private function send(string $text, string $target): string
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->post_message($text, $target, self::SENDER);
    }

    /**
     * Check SMS form server
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function check(string $dateFrom, string $dateTo): string
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->status_sms_date($dateFrom, $dateTo);
    }

    /**
     * Get SMS form inbox
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function getMessages(string $dateFrom, string $dateTo): string
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->status_inbox(false,0,$dateFrom,$dateTo);
    }

    /**
     * SMS Sender and result parser
     * @return bool
     */
    public function sendSMS(): bool
    {
        $result = new SimpleXMLElement($this->send(
            $this->text,
            self::PHONE_PREFIX_RU . $this->target
        ));

        $sMSNotification = new SMSNotification();
        $sMSNotification->setUser(
            $this->em->getRepository(AuthUser::class)->findOneBy([
                'phone' => $this->target
            ])
        );
        $sMSNotification->setText($this->text);
        $sMSNotification->setCreatedAt(new DateTime('now'));
        $sMSNotification->setStatus(self::WAIT);
        $sMSNotification->setExternalId((string) $result->result->sms['id']);

        $this->em->persist($sMSNotification);
        $this->em->flush();

        return true;
    }

    /**
     * SMS RE-Sender and result parser
     * @param SMSNotification $notification
     * @return bool
     */
    public function reSendSMS(SMSNotification $notification): bool
    {
        $result = new SimpleXMLElement($this->send(
            $notification->getText(),
            self::PHONE_PREFIX_RU . $notification->getUser()->getPhone()
        ));

        $notification->setCreatedAt(new DateTime('now'));
        $notification->setExternalId((string) $result->result->sms['id']);
        $notification->setAttempt((int) $notification->getAttempt() + 1);

        $this->em->persist($notification);
        $this->em->flush();
        return true;
    }

    /**
     * SMS Checker and result parser
     * @return SimpleXMLElement
     */
    public function checkSMS()
    {
        return new SimpleXMLElement($this->check(
            (new DateTime('now'))
                ->sub(new DateInterval('PT'. self::PREIOD_TO_UPDATE_EMAIL .'H'))
                ->format('d.m.Y H:i:s'),
            (new DateTime('now'))->format('d.m.Y H:i:s')
        ));
    }

    /**
     * SMS Getter and result parser
     * @return SimpleXMLElement
     */
    public function getUnreadSMS()
    {
        return new SimpleXMLElement($this->getMessages(
            (new DateTime('now'))
                ->sub(new DateInterval('PT'. self::PREIOD_TO_CHACK_EMAIL .'H'))
                ->format('d.m.Y H:i:s'),
            (new DateTime('now'))->format('d.m.Y H:i:s')
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
     * @param string $target
     * @return SMSNotificationService
     */
    public function setTarget(string $target): SMSNotificationService
    {
        $this->target = $target;
        return $this;
    }
}