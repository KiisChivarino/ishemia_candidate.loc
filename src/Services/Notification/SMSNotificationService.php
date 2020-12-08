<?php


namespace App\Services\Notification;


use App\API\BEESMS;
use App\Entity\AuthUser;
use App\Entity\SMSNotification;
use App\Services\InfoService\AuthUserInfoService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\VarDumper\VarDumper;

class SMSNotificationService
{
    const
        SENDER = '3303',
        SMS_USER = '775000',
        SMS_PASSWORD = 'Yandex10241024'
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
     * Logger constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $text
     * @param string $target
     * @return bool
     */
    private function send(string $text, string $target)
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->post_message($text, $target, self::SENDER);
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @return bool
     */
    private function check(string $dateFrom, string $dateTo)
    {
        $sms = new BEESMS(self::SMS_USER,self::SMS_PASSWORD);
        return $sms->status_sms_date($dateFrom, $dateTo);
    }

    /**
     * @return bool
     */
    public function sendSMS()
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
        $sMSNotification->setStatus('wait');
        $sMSNotification->setExternalId((string) $result->result->sms['id']);

        $this->em->persist($sMSNotification);
        $this->em->flush();

        return true;
    }

    /**
     * @param SMSNotification $notification
     * @return bool
     */
    public function reSendSMS(SMSNotification $notification)
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
     */
    public function setText(string $text): SMSNotificationService
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): SMSNotificationService
    {
        $this->target = $target;
        return $this;
    }

}