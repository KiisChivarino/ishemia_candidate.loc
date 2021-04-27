<?php

namespace App\Controller\Admin;

use App\Services\DataTable\Admin\WebNotificationDataTableService;
use App\Services\TemplateBuilders\Admin\WebNotificationTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Контроллеры сущности "Web уведомление"
 * @Route("/admin/web_notifications")
 * @IsGranted("ROLE_MANAGER")
 */
class WebNotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/web_notifications/';

    /**
     * EmailNotificationController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->templateService = new WebNotificationTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список web-уведомлений
     * @Route("/", name="web_notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param WebNotificationDataTableService $webNotificationDataTableService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        WebNotificationDataTableService $webNotificationDataTableService
    ): Response
    {
        return $this->responseList($request, $webNotificationDataTableService);
    }
}
