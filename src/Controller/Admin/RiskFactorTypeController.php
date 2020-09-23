<?php

namespace App\Controller\Admin;

use App\Entity\RiskFactorType;
use App\Form\Admin\RiskFactorTypeType;
use App\Services\DataTable\Admin\RiskFactorTypeDataTableService;
use App\Services\TemplateBuilders\RiskFactorTypeTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class RiskFactorTypeController
 * Контроллеры для сущности "Тип (группа) фактора риска"
 * @Route("/admin/risk_factor_type")
 *
 * @package App\Controller\Admin
 */
class RiskFactorTypeController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/risk_factor_type/';

    /**
     * RiskFactorTypeController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new RiskFactorTypeTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Risk factor type (group) list
     * @Route("/", name="risk_factor_type_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param RiskFactorTypeDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, RiskFactorTypeDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New risk factor type
     * @Route("/new", name="risk_factor_type_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new RiskFactorType()),
            RiskFactorTypeType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Show risk factor type info
     * @Route("/{id}", name="risk_factor_type_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param RiskFactorType $riskFactorType
     *
     * @return Response
     */
    public function show(RiskFactorType $riskFactorType): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $riskFactorType);
    }

    /**
     * Edit risk factor type
     * @Route("/{id}/edit", name="risk_factor_type_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param RiskFactorType $riskFactorType
     *
     * @return Response
     */
    public function edit(Request $request, RiskFactorType $riskFactorType): Response
    {
        return $this->responseEdit($request, $riskFactorType, RiskFactorTypeType::class);
    }

    /**
     * Delete risk factor type
     * @Route("/{id}", name="risk_factor_type_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param RiskFactorType $riskFactorType
     *
     * @return Response
     */
    public function delete(Request $request, RiskFactorType $riskFactorType): Response
    {
        return $this->responseDelete($request, $riskFactorType);
    }
}
