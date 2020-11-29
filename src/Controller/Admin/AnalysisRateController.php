<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisRate;
use App\Form\Admin\AnalysisRate\AnalysisRateType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\AnalysisRateDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AnalysisRateTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры предельных нормальных значений
 * @Route("/admin/analysis_rate")
 * @IsGranted("ROLE_ADMIN")
 */
class AnalysisRateController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/analysis_rate/';

    /**
     * AnalysisRateController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new AnalysisRateTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List reference values
     * @Route("/", name="analysis_rate_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param AnalysisRateDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(
        Request $request,
        FilterService $filterService,
        AnalysisRateDataTableService $dataTableService): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray([self::FILTER_LABELS['ANALYSIS_GROUP']])
        );
    }

    /**
     * New reference values item
     * @Route("/new", name="analysis_rate_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FilterService $filterService): Response
    {
        return $this->responseNew(
            $request,
            (new AnalysisRate()),
            AnalysisRateType::class,
            (new FilterLabels($filterService))->setFilterLabelsArray([AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']]),
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Show reference values item
     * @Route("/{id}", name="analysis_rate_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     */
    public function show(AnalysisRate $analysisRate): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $analysisRate);
    }

    /**
     * Edit reference values item
     * @Route("/{id}/edit", name="analysis_rate_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, AnalysisRate $analysisRate): Response
    {
        return $this->responseEdit($request, $analysisRate, AnalysisRateType::class);
    }

    /**
     * Delete reference values item
     * @Route("/{id}", name="analysis_rate_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     */
    public function delete(Request $request, AnalysisRate $analysisRate): Response
    {
        return $this->responseDelete($request, $analysisRate);
    }
}