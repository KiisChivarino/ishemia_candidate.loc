<?php

namespace App\Controller\Admin;

use App\Entity\TemplateParameter;
use App\Form\Admin\TemplateParameterType;
use App\Services\DataTable\Admin\TemplateParameterDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\TemplateParameterTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Параметр шаблона"
 * @Route("/admin/template_parameter")
 * @IsGranted("ROLE_ADMIN")
 */
class TemplateParameterController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/template_parameter/';

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new TemplateParameterTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }
    /**
     * Новый параметр шаблона
     * @Route("/new", name="template_parameter_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new TemplateParameter()),
            TemplateParameterType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Список параметров шаблонов
     * @Route("/", name="template_parameter_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TemplateParameterDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, TemplateParameterDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="template_parameter_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param TemplateParameter $templateParameter
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(TemplateParameter $templateParameter, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $templateParameter,
            [
                'templateParameterFilterName' => $filterService->generateFilterName('template_parameter', TemplateParameter::class)
            ]
        );
    }

    /**
     * Edit template parameter
     * @Route("/{id}/edit", name="template_parameter_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateParameter $templateParameter
     * @return Response
     */
    public function edit(Request $request, TemplateParameter $templateParameter): Response
    {
        return $this->responseEdit($request, $templateParameter, TemplateParameterType::class);
    }

    /**
     * Delete template type
     * @Route("/{id}", name="template_parameter_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateParameter $templateParameter
     * @return Response
     */
    public function delete(Request $request, TemplateParameter $templateParameter): Response
    {
        return $this->responseDelete($request, $templateParameter);
    }
}
