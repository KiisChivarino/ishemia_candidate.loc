<?php

namespace App\Controller\Admin;

use App\Entity\EmailNotification;
use App\Services\DataTable\Admin\EmailNotificationDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\EmailNotificationTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Контроллеры сущности "E-mail уведомление"
 * @Route("/admin/email_notifications")
 * @IsGranted("ROLE_ADMIN")
 */
class EmailNotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/email_notifications/';

    /**
     * EmailNotificationController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new EmailNotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список email-уведомлений
     * @Route("/", name="email_notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param EmailNotificationDataTableService $emailNotificationDataTableService
     * @return Response
     */
    public function list(Request $request, EmailNotificationDataTableService $emailNotificationDataTableService): Response
    {
        return $this->responseList($request, $emailNotificationDataTableService);
    }

    /**
     * Email Notification info
     * @Route("/{id}", name="email_notification_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param EmailNotification $emailNotification
     * @param FilterService $filterService
     * @return Response
     */
    public function show(EmailNotification $emailNotification, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $emailNotification,
            [
                'templateParameterFilterName' => $filterService->generateFilterName('emailNotifications', EmailNotification::class)
            ]
        );
    }

    /**
     * Delete Email Notification
     * @Route("/{id}", name="email_notification_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param EmailNotification $emailNotification
     * @return Response
     */
    public function delete(Request $request, EmailNotification $emailNotification): Response
    {
        return $this->responseDelete($request, $emailNotification);
    }
}
