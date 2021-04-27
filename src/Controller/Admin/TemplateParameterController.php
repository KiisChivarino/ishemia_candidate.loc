<?php

namespace App\Controller\Admin;

use App\Entity\TemplateParameter;
use App\Form\Admin\TemplateParameterType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\TemplateParameterDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\TemplateParameterTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        TemplateParameterDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['TEMPLATE_TYPE'],]
            )
        );
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="template_parameter_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param TemplateParameter $templateParameter
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function show(TemplateParameter $templateParameter, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $templateParameter,
            [
                'templateParameterFilterName' => $filterService->generateFilterName(
                    'template_parameter_text_list',
                    TemplateParameter::class
                )
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
     * @throws Exception
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
     * @throws Exception
     */
    public function delete(Request $request, TemplateParameter $templateParameter): Response
    {
        return $this->responseDelete($request, $templateParameter);
    }
}
