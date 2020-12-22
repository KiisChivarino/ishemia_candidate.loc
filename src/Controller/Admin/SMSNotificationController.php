<?php

namespace App\Controller\Admin;

use App\Services\DataTable\Admin\SMSNotificationDataTableService;
use App\Services\TemplateBuilders\Admin\SMSNotificationTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new SMSNotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список sms-уведомлений
     * @Route("/", name="sms_notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param SMSNotificationDataTableService $smsNotificationDataTableService
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, SMSNotificationDataTableService $smsNotificationDataTableService): Response
    {
        return $this->responseList($request, $smsNotificationDataTableService);
    }
}
