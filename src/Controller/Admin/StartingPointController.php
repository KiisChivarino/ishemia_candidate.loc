<?php

namespace App\Controller\Admin;

use App\Entity\StartingPoint;
use App\Form\Admin\StartingPointType;
use App\Services\DataTable\Admin\StartingPointDataTableService;
use App\Services\TemplateBuilders\Admin\StartingPointTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class StartingPointController
 * @Route("/admin/starting_point")
 * @IsGranted("ROLE_MANAGER")
 * @package App\Controller\Admin
 */
class StartingPointController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/starting_point/';

    /**
     * StartingPointController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->templateService = new StartingPointTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of starting points of testing dates by plan
     * @Route("/", name="starting_point_list", methods={"GET","POST"})
     * @param Request $request
     * @param StartingPointDataTableService $dataTableService
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, StartingPointDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Show starting point of testing dates by plan
     * @Route("/{id}", name="starting_point_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param StartingPoint $startingPoint
     * @return Response
     * @throws Exception
     */
    public function show(StartingPoint $startingPoint): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $startingPoint);
    }

    /**
     * Edit starting point of testing dates by plan
     * @Route("/{id}/edit", name="starting_point_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param StartingPoint $startingPoint
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, StartingPoint $startingPoint): Response
    {
        return $this->responseEdit($request, $startingPoint, StartingPointType::class);
    }
}
