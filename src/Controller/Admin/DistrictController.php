<?php

namespace App\Controller\Admin;

use App\Entity\District;
use App\Form\Admin\DistrictType;
use App\Services\DataTable\Admin\DistrictDataTableService;
use App\Services\TemplateBuilders\DistrictTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class DistrictController
 * @Route("/admin/district")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class DistrictController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/district/';

    /**
     * DistrictController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new DistrictTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список районов
     * @Route("/", name="district_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param DistrictDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, DistrictDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новый район
     * @Route("/new", name="district_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new District()), DistrictType::class);
    }

    /**
     * Просмотр района
     * @Route("/{id}", name="district_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param District $district
     *
     * @return Response
     */
    public function show(District $district): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $district);
    }

    /**
     * Редактирование района
     * @Route("/{id}/edit", name="district_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param District $district
     *
     * @return Response
     */
    public function edit(Request $request, District $district): Response
    {
        return $this->responseEdit($request, $district, DistrictType::class);
    }

    /**
     * Удаление района
     * @Route("/{id}", name="district_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param District $district
     *
     * @return Response
     */
    public function delete(Request $request, District $district): Response
    {
        return $this->responseDelete($request, $district);
    }
}
