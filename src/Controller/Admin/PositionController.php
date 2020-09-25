<?php

namespace App\Controller\Admin;

use App\Entity\Position;
use App\Form\Admin\PositionType;
use App\Services\DataTable\Admin\PositionDataTableService;
use App\Services\TemplateBuilders\PositionTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PositionController
 * @Route("/position")
 *
 * @package App\Controller\Admin
 */
class PositionController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/position/';

    /**
     * PositionController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PositionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of positions
     * @Route("/", name="position_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PositionDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, PositionDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New position
     * @Route("/new", name="position_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Position()), PositionType::class);
    }

    /**
     * Show position
     * @Route("/{id}", name="position_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Position $position
     *
     * @return Response
     */
    public function show(Position $position): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $position);
    }

    /**
     * Edit position
     * @Route("/{id}/edit", name="position_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Position $position
     *
     * @return Response
     */
    public function edit(Request $request, Position $position): Response
    {
        return $this->responseEdit($request, $position, PositionType::class);
    }

    /**
     * Delete position
     * @Route("/{id}", name="position_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Position $position
     *
     * @return Response
     */
    public function delete(Request $request, Position $position): Response
    {
        return $this->responseDelete($request, $position);
    }
}