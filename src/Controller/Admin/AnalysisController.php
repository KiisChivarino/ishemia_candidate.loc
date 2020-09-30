<?php

namespace App\Controller\Admin;

use App\Entity\Analysis;
use App\Form\Admin\Analysis\AnalysisType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\AnalysisDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AnalysisTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Анализ"
 * @Route("/admin/analysis")
 * @IsGranted("ROLE_ADMIN")
 */
class AnalysisController extends AdminAbstractController
{

    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/analysis/';

    /**
     * AnalysisController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new AnalysisTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список анализов
     * @Route("/", name="analysis_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param AnalysisDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, FilterService $filterService, AnalysisDataTableService $dataTableService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            )
        );
    }

    /**
     * Добавление анализа
     * @Route("/new", name="analysis_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new Analysis()),
            AnalysisType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Информация об анализе
     * @Route("/{id}", name="analysis_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Analysis $analysis
     *
     * @return Response
     */
    public function show(Analysis $analysis): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $analysis);
    }

    /**
     * Редактирование анализа
     * @Route("/{id}/edit", name="analysis_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Analysis $analysis
     *
     * @return Response
     */
    public function edit(Request $request, Analysis $analysis): Response
    {
        return $this->responseEdit($request, $analysis, AnalysisType::class);
    }

    /**
     * Удаление анализа
     * @Route("/{id}", name="analysis_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Analysis $analysis
     *
     * @return Response
     */
    public function delete(Request $request, Analysis $analysis): Response
    {
        return $this->responseDelete($request, $analysis);
    }
}
