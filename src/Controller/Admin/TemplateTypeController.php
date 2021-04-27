<?php

namespace App\Controller\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\TemplateType;
use App\Form\Admin\AnalysisGroupType;
use App\Form\Admin\TemplateTypeType;
use App\Services\DataTable\Admin\TemplateTypeDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\TemplateTypeTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Тип шаблона"
 * @Route("/admin/template_type")
 * @IsGranted("ROLE_ADMIN")
 */
class TemplateTypeController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/template_type/';

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
        $this->templateService = new TemplateTypeTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Новая группа анализов
     * @Route("/new", name="template_type_new", methods={"GET","POST"})
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
            (new AnalysisGroup()),
            AnalysisGroupType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Список типов шаблонов
     * @Route("/", name="template_type_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TemplateTypeDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, TemplateTypeDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Template Type info
     * @Route("/{id}", name="template_type_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param TemplateType $templateType
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function show(TemplateType $templateType, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $templateType,
            [
                'templateTypeFilterName' => $filterService->generateFilterName(
                    'template_parameter_list',
                    TemplateType::class)
            ]
        );
    }

    /**
     * Edit template type
     * @Route("/{id}/edit", name="template_type_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateType $templateType
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, TemplateType $templateType): Response
    {
        return $this->responseEdit($request, $templateType, TemplateTypeType::class);
    }

    /**
     * Delete template type
     * @Route("/{id}", name="template_type_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TemplateType $templateType
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, TemplateType $templateType): Response
    {
        return $this->responseDelete($request, $templateType);
    }
}
