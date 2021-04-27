<?php

namespace App\Controller\Admin;

use App\Entity\Position;
use App\Form\Admin\PositionType;
use App\Services\DataTable\Admin\PositionDataTableService;
use App\Services\TemplateBuilders\Admin\PositionTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PositionController
 * @Route("admin/position")
 * @IsGranted("ROLE_ADMIN")
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
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function delete(Request $request, Position $position): Response
    {
        return $this->responseDelete($request, $position);
    }
}
