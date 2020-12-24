<?php

namespace App\Controller\Admin;

use App\Repository\PatientRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Notification\EmailNotificationService;
use App\Services\Notification\NotificationService;
use App\Services\Notification\SMSNotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Michelf\Markdown;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
{
    /** @var KernelInterface  */
    private $appKernel;

    /** @var SMSNotificationService */
    private $sms;

    /** @var EmailNotificationService */
    private $email;

    /** @var NotificationService */
    private $notification;

    /**
     * AdminController constructor.
     * @param KernelInterface $appKernel
     * @param SMSNotificationService $sms
     * @param EmailNotificationService $emailNotificationService
     * @param NotificationService $notificationService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        KernelInterface $appKernel,
        SMSNotificationService $sms,
        EmailNotificationService $emailNotificationService,
        NotificationService $notificationService,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->sms = $sms;
        $this->appKernel = $appKernel;
        $this->email = $emailNotificationService;
        $this->notification = $notificationService;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => Markdown::defaultTransform(file_get_contents($this->appKernel->getProjectDir() .
                    '/data/documents/changes.md'))
            ]
        );
    }

    /**
     * @Route("/testNotification", name="testNotification")
     * @param PatientRepository $patientRepository
     * @return Response
     */
    public function testNotification(PatientRepository $patientRepository): Response
    {
        $this->notification
            ->setPatient($patientRepository->findAll()[1])
            ->setNotificationTemplate('test')
            ->setNotificationReceiverType('patient')
            ->setMedicalHistory($patientRepository->findAll()[1]->getMedicalHistories()[0])
            ->setMedicalRecord($patientRepository->findAll()[1]->getMedicalHistories()[0]->getMedicalRecords()[0])
            ->setTexts([(new AuthUserInfoService())->getFIO($patientRepository->findAll()[1]->getAuthUser())])
            ->notifyPatient()
        ;
        return new Response(true);
    }
}