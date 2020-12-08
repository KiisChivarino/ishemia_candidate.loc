<?php

namespace App\Controller\Admin;

use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
use App\Form\Admin\LogActionType;
use App\Services\DataTable\Admin\LogActionDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\LogActionTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Типы логов"
 * @Route("/admin/log_action")
 * @IsGranted("ROLE_ADMIN")
 */
class LogActionController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/log_action/';

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new LogActionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список параметров шаблонов
     * @Route("/", name="log_action_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param LogActionDataTableService $logActionDataTableService
     * @return Response
     */
    public function list(Request $request, LogActionDataTableService $logActionDataTableService): Response
    {
        return $this->responseList($request, $logActionDataTableService);
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="log_action_show", methods={"GET"}, requirements={"id"="\d+"})
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
                'logActionFilterName' => $filterService->generateFilterName('log_action', LogAction::class)
            ]
        );
    }

    /**
     * Edit template parameter
     * @Route("/{id}/edit", name="log_action_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param LogAction $logAction
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, LogAction $logAction): Response
    {
        return $this->responseEdit($request, $logAction, LogActionType::class);
    }

    /**
     * Delete template type
     * @Route("/{id}", name="log_action_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Log $log_action
     * @return Response
     */
    public function delete(Request $request, Log $log_action): Response
    {
        return $this->responseDelete($request, $log_action);
    }
}
