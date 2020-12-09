<?php


namespace App\Services\Notification;


use App\API\BEESMS;
use App\Entity\AuthUser;
use App\Entity\SMSNotification;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class SMSNotificationService
{
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
     * @param $dateFrom
     * @param $dateTo
     * @return bool
     */
    private function check(string $dateFrom, string $dateTo): string
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->status_sms_date($dateFrom, $dateTo);
    }

    /**
     * @return bool
     */
    public function sendSMS(): bool
    {
        $result = new SimpleXMLElement($this->send(
            $this->text,
            '+7' . $this->target
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
     * @param SMSNotification $notification
     * @return bool
     */
    public function reSendSMS(SMSNotification $notification): bool
    {
        $result = new SimpleXMLElement($this->send(
            $notification->getText(),
            '+7' . $notification->getUser()->getPhone()
        ));

        $notification->setCreatedAt(new DateTime('now'));
        $notification->setExternalId((string) $result->result->sms['id']);
        $notification->setAttempt((int) $notification->getAttempt() + 1);

        $this->em->persist($notification);
        $this->em->flush();
        return true;
    }

    public function checkSMS()
    {
        $result = new SimpleXMLElement($this->check(
            (new DateTime('today'))->format('d.m.Y') . ' 00:00:00',
            (new DateTime('today'))->format('d.m.Y') . ' 23:59:59'
        ));

        return $result;
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