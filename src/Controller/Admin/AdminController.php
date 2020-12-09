<?php

namespace App\Controller\Admin;

use App\Services\Notification\SMSNotificationService;
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