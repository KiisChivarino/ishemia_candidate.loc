<?php

namespace App\Controller\Admin;

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

    /** KernelInterface $appKernel */
    private $appKernel;

    /**
     * AdminController constructor.
     * @param KernelInterface $appKernel
     * @param SMSNotificationService $sms
     */
    public function __construct(KernelInterface $appKernel, SMSNotificationService $sms)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function index()
    {

//        $sms = $this->sms
//            ->setText('123')
//            ->setTarget('0000000000')
//            ->sendSMS();
////        $this->sms->checkSMS();
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => Markdown::defaultTransform(file_get_contents($this->appKernel->getProjectDir().'/data/documents/changes.md'))
            ]
        );
    }
}