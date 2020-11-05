<?php

namespace App\Controller\Admin;

use App\Entity\Region;
use App\Form\Admin\RegionType;
use App\Services\DataTable\Admin\RegionDataTableService;
use App\Services\TemplateBuilders\Admin\RegionTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class RegionController
 * actions of region
 * @Route("/admin/region")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class RegionController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/region/';

    /**
     * RegionController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new RegionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Region list
     * @Route("/", name="region_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param RegionDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, RegionDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New region
     * @Route("/new", name="region_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Region()), RegionType::class);
    }

    /**
     * Show region info
     * @Route("/{id}", name="region_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Region $region
     *
     * @return Response
     */
    public function show(Region $region): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $region);
    }

    /**
     * Edit region
     * @Route("/{id}/edit", name="region_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Region $region
     *
     * @return Response
     */
    public function edit(Request $request, Region $region): Response
    {
        return $this->responseEdit($request, $region, RegionType::class);
    }

    /**
     * Delete region
     * @Route("/{id}", name="region_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Region $region
     *
     * @return Response
     */
    public function delete(Request $request, Region $region): Response
    {
        return $this->responseDelete($request, $region);
    }
}
