<?php

namespace App\Controller\Admin;

use App\Services\DataTable\Admin\EmailNotificationDataTableService;
use App\Services\TemplateBuilders\Admin\EmailNotificationTemplate;
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
 * Контроллеры сущности "E-mail уведомление"
 * @Route("/admin/email_notifications")
 * @IsGranted("ROLE_MANAGER")
 */
class EmailNotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/email_notifications/';

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
        AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($translator);
        $this->templateService = new EmailNotificationTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список email-уведомлений
     * @Route("/", name="email_notification_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EmailNotificationDataTableService $emailNotificationDataTableService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        EmailNotificationDataTableService $emailNotificationDataTableService
    ): Response {
        return $this->responseList($request, $emailNotificationDataTableService);
    }
}
