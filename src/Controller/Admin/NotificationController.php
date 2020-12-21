<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Entity\Patient;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\NotificationDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\NotificationTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Контроллеры сущности "Уведомление"
 * @Route("/admin/notification")
 * @IsGranted("ROLE_ADMIN")
 */
class NotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/notification/';

    /**
     * NotificationController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new NotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список уведомлений
     * @Route("/", name="notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param NotificationDataTableService $notificationDataTableService
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        NotificationDataTableService $notificationDataTableService,
        FilterService $filterService
    ): Response {
        return $this->responseList(
            $request, $notificationDataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['PATIENT'],]
            )
        );
    }

    /**
     * Notification info
     * @Route("/{id}", name="notification_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Notification $notification
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function show(Notification $notification, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $notification,
            [
                'templateParameterFilterName' => $filterService->generateFilterName(
                    'patient',
                    Patient::class
                )
            ]
        );
    }
}
