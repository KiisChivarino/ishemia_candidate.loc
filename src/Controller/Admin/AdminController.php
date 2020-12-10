<?php

namespace App\Controller\Admin;

use App\API\BEESMS;
use App\Entity\Patient;
use App\Entity\ReceivedSMS;
use App\Services\Notification\SMSNotificationService;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Michelf\Markdown;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
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

    /** @var string prefix for RU phone numbers */
    const PHONE_PREFIX_RU = '+7';

    /** KernelInterface $appKernel */
    private $appKernel;

    public function __construct(KernelInterface $appKernel, SMSNotificationService $sms)
    {
        parent::__construct($sms);
        $this->appKernel = $appKernel;
    }

    /**
     * @Route("/admin", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {


//        $this->sms->checkSMS();
///
///
//
//        $sms = $this->sms->getUnreadSMS();
//        dd($sms);
//        return new Response(dd(new \SimpleXMLElement($data)));
//        $em = $this->container->get('doctrine')->getManager();
//        $patients = $em->getRepository(Patient::class)->findAll();
//
//        $result = $this->sms->getUnreadSMS();
//        foreach ($result->MESSAGES->MESSAGE as $message) {
//            if ((string) $message->SMS_TARGET == self::SENDER) {
//                foreach ($patients as $patient) {
//                    if ((string) $message->SMS_SENDER == (string) self::PHONE_PREFIX_RU . $patient->getAuthUser()->getPhone()) {
//                        $sms = new ReceivedSMS();
//                        $sms->setPatient($patient);
//                        $sms->setText((string) $message->SMS_TEXT);
//                        $sms->setCreatedAt(new \DateTime('now'));
//                        $em->persist($sms);
//                    }
//                }
//            }
//        }
////        dd($sms);
//        $em->flush();
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => Markdown::defaultTransform(file_get_contents($this->appKernel->getProjectDir() . '/data/documents/changes.md'))
            ]
        );
    }

    /**
     * @Route("/admin/testsms", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testsms()
    {
        header("Content-Type: text/xml; charset=UTF-8");
        $sms = $this->sms
            ->setText('Тестовое СМС')
            ->setTarget('0000000000')
//            ->setTarget('9611672720')
            ->sendSMS();

//        $this->sms->checkSMS();
///
///
//
//        $sms = $this->sms->getUnreadSMS();
//        dd($sms);
//        return new Response(dd(new \SimpleXMLElement($data)));
//        $em = $this->container->get('doctrine')->getManager();
//        $patients = $em->getRepository(Patient::class)->findAll();
//
//        $result = $this->sms->getUnreadSMS();
//        foreach ($result->MESSAGES->MESSAGE as $message) {
//            if ((string) $message->SMS_TARGET == self::SENDER) {
//                foreach ($patients as $patient) {
//                    if ((string) $message->SMS_SENDER == (string) self::PHONE_PREFIX_RU . $patient->getAuthUser()->getPhone()) {
//                        $sms = new ReceivedSMS();
//                        $sms->setPatient($patient);
//                        $sms->setText((string) $message->SMS_TEXT);
//                        $sms->setCreatedAt(new \DateTime('now'));
//                        $em->persist($sms);
//                    }
//                }
//            }
//        }
////        dd($sms);
//        $em->flush();
        return new Response(true);
    }
}