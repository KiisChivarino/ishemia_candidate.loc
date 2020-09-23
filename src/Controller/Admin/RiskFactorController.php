<?php

namespace App\Controller\Admin;

use App\Entity\RiskFactor;
use App\Form\Admin\RiskFactorType;
use App\Services\DataTable\Admin\RiskFactorDataTableService;
use App\Services\TemplateBuilders\RiskFactorTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class RiskFactorController
 * Контроллеры для сущности "Фактор риска"
 * @Route("/admin/risk_factor")
 *
 * @package App\Controller\Admin
 */
class RiskFactorController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/risk_factor/';

    /**
     * RiskFactorController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new  RiskFactorTemplate($router->getRouteCollection(),  get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }
    /**
     * Risk factor list
     * @Route("/", name="risk_factor_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param RiskFactorDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, RiskFactorDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New risk factor
     * @Route("/new", name="risk_factor_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new RiskFactor()), RiskFactorType::class);
    }

    /**
     * Show risk factor info
     * @Route("/{id}", name="risk_factor_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param RiskFactor $riskFactor
     *
     * @return Response
     */
    public function show(RiskFactor $riskFactor): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $riskFactor);
    }

    /**
     * Edit risk factor
     * @Route("/{id}/edit", name="risk_factor_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param RiskFactor $riskFactor
     *
     * @return Response
     */
    public function edit(Request $request, RiskFactor $riskFactor): Response
    {
        return $this->responseEdit($request, $riskFactor, RiskFactorType::class);
    }

    /**
     * Delete risk factor
     * @Route("/{id}", name="risk_factor_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param RiskFactor $riskFactor
     *
     * @return Response
     */
    public function delete(Request $request, RiskFactor $riskFactor): Response
    {
        return $this->responseDelete($request, $riskFactor);
    }
}
