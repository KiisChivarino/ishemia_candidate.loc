<?php

namespace App\Controller\Admin;

use App\Entity\TemplateParameterText;
use App\Form\Admin\TemplateParameterTextType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\TemplateParameterTextDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\TemplateParameterTextTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Текст параметра шаблона"
 * @Route("/admin/template_parameter_text")
 * @IsGranted("ROLE_ADMIN")
 */
class TemplateParameterTextController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/template_parameter_text/';

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new TemplateParameterTextTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Новый текст параметра шаблона
     * @Route("/new", name="template_parameter_text_new", methods={"GET","POST"})
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
            (new TemplateParameterText()),
            TemplateParameterTextType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Список текстов параметров шаблонов
     * @Route("/", name="template_parameter_text_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TemplateParameterTextDataTableService $dataTableService
     *
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        TemplateParameterTextDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['TEMPLATE_PARAMETER'],]
            )
        );
    }

    /**
     * Template parameter text info
     * @Route("/{id}", name="template_parameter_text_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param TemplateParameterText $templateParameterText
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function show(TemplateParameterText $templateParameterText, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $templateParameterText,
            [
                'templateParameterFilterName' => $filterService->generateFilterName(
                    'template_parameter_text',
                    TemplateParameterText::class
                )
            ]
        );
    }

    /**
     * Edit template parameter text
     * @Route("/{id}/edit", name="template_parameter_text_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateParameterText $templateParameterText
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, TemplateParameterText $templateParameterText): Response
    {
        return $this->responseEdit($request, $templateParameterText, TemplateParameterTextType::class);
    }

    /**
     * Delete template parameter text
     * @Route("/{id}", name="template_parameter_text_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateParameterText $templateParameterText
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, TemplateParameterText $templateParameterText): Response
    {
        return $this->responseDelete($request, $templateParameterText);
    }
}
