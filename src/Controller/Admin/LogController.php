<?php

namespace App\Controller\Admin;

use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
use App\Services\DataTable\Admin\LogDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\LogTemplate;
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
     * AnalysisGroupController constructor.
     *
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
     * @return Response
     */
    public function list(Request $request, LogDataTableService $logDataTableService): Response
    {
        return $this->responseList($request, $logDataTableService);
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="log_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param LogAction $logAction
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(LogAction $logAction, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $logAction,
            [
                'templateParameterFilterName' => $filterService->generateFilterName('log', LogAction::class)
            ]
        );
    }

    /**
     * Delete template type
     * @Route("/{id}", name="log_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Log $log
     * @return Response
     */
    public function delete(Request $request, Log $log): Response
    {
        return $this->responseDelete($request, $log);
    }
}
