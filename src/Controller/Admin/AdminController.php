<?php

namespace App\Controller\Admin;

use App\Repository\PatientRepository;
use App\Services\Notification\EmailNotificationService;
use App\Services\Notification\NotificationService;
use App\Services\Notification\SMSNotificationService;
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
     */
    public function __construct(
        KernelInterface $appKernel,
        SMSNotificationService $sms,
        EmailNotificationService $emailNotificationService,
        NotificationService $notificationService
    )
    {
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
}