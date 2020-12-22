<?php

namespace App\Controller\Admin;

use App\Entity\District;
use App\Form\Admin\DistrictType;
use App\Services\DataTable\Admin\DistrictDataTableService;
use App\Services\TemplateBuilders\Admin\DistrictTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function delete(Request $request, District $district): Response
    {
        return $this->responseDelete($request, $district);
    }
}
