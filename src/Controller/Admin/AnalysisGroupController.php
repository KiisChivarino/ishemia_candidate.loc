<?php

namespace App\Controller\Admin;

use App\Entity\AnalysisGroup;
use App\Form\Admin\AnalysisGroupType;
use App\Services\DataTable\Admin\AnalysisGroupDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AnalysisGroupTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Группа анализов"
 * @Route("/admin/analysis_group")
 * @IsGranted("ROLE_ADMIN")
 */
class AnalysisGroupController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/analysis_group/';

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new AnalysisGroupTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список групп анализов
     * @Route("/", name="analysis_group_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param AnalysisGroupDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, AnalysisGroupDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новая группа анализов
     * @Route("/new", name="analysis_group_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new AnalysisGroup()),
            AnalysisGroupType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Analysis group info
     * @Route("/{id}", name="analysis_group_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param AnalysisGroup $analysisGroup
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(AnalysisGroup $analysisGroup, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $analysisGroup,
            [
                'analysisGroupFilterName' => $filterService->generateFilterName('analysis_list', AnalysisGroup::class)
            ]
        );
    }

    /**
     * Edit analysis group
     * @Route("/{id}/edit", name="analysis_group_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisGroup $analysisGroup
     *
     * @return Response
     */
    public function edit(Request $request, AnalysisGroup $analysisGroup): Response
    {
        return $this->responseEdit($request, $analysisGroup, AnalysisGroupType::class);
    }

    /**
     * Delete analysis group
     * @Route("/{id}", name="analysis_group_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisGroup $analysisGroup
     *
     * @return Response
     */
    public function delete(Request $request, AnalysisGroup $analysisGroup): Response
    {
        return $this->responseDelete($request, $analysisGroup);
    }
}
