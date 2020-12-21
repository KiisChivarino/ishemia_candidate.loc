<?php

namespace App\Controller\Admin;

use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\LogDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\LogTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Параметр шаблона"
 * @Route("/admin/log")
 * @IsGranted("ROLE_ADMIN")
 */
class LogController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/log/';

    /**
     * LogController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new LogTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список параметров шаблонов
     * @Route("/", name="log_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param LogDataTableService $logDataTableService
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, LogDataTableService $logDataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $logDataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['LOG_ACTION'],]
            )
        );
    }
}
