<?php

namespace App\Controller\Admin;

use App\Entity\NotificationType;
use App\Form\Admin\NotificationTypeType;
use App\Services\DataTable\NotificationTypeDataTableService;
use App\Services\TemplateBuilders\Admin\NotificationTypeTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class NotificationTypeController
 * @Route("/admin/notification_type")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class NotificationTypeController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/notification_type/';

    /**
     * NotificationTypeController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new NotificationTypeTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of notification types
     * @Route("/", name="notification_type_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param NotificationTypeDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, NotificationTypeDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New notification type
     * @Route("/new", name="notification_type_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new NotificationType()), NotificationTypeType::class);
    }

    /**
     * Show notification type
     * @Route("/{id}", name="notification_type_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param NotificationType $notificationType
     *
     * @return Response
     */
    public function show(NotificationType $notificationType): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $notificationType);
    }

    /**
     * Edit notification type
     * @Route("/{id}/edit", name="notification_type_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param NotificationType $notificationType
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, NotificationType $notificationType): Response
    {
        return $this->responseEdit($request, $notificationType, NotificationTypeType::class);
    }

    /**
     * Delete notification type
     * @Route("/{id}", name="notification_type_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param NotificationType $notificationType
     *
     * @return Response
     */
    public function delete(Request $request, NotificationType $notificationType): Response
    {
        return $this->responseDelete($request, $notificationType);
    }
}
