<?php

namespace App\Controller\Admin;

use App\Services\DataTable\Admin\SMSNotificationDataTableService;
use App\Services\TemplateBuilders\Admin\SMSNotificationTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Контроллеры сущности "E-mail уведомление"
 * @Route("/admin/sms_notifications")
 * @IsGranted("ROLE_ADMIN")
 */
class SMSNotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/sms_notifications/';

    /**
     * EmailNotificationController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new SMSNotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список sms-уведомлений
     * @Route("/", name="sms_notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param SMSNotificationDataTableService $smsNotificationDataTableService
     * @return Response
     */
    public function list(Request $request, SMSNotificationDataTableService $smsNotificationDataTableService): Response
    {
        return $this->responseList($request, $smsNotificationDataTableService);
    }
}
