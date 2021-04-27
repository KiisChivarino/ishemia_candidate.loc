<?php

namespace App\Controller\Admin;

use App\Entity\Logger\LogAction;
use App\Form\Admin\LogActionType;
use App\Services\DataTable\Admin\LogActionDataTableService;
use App\Services\TemplateBuilders\Admin\LogActionTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
     */
    public function list(Request $request, LogActionDataTableService $logActionDataTableService): Response
    {
        return $this->responseList($request, $logActionDataTableService);
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
}
