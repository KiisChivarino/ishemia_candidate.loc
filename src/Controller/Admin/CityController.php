<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\Admin\CityType;
use App\Services\DataTable\Admin\CityDataTableService;
use App\Services\TemplateBuilders\Admin\CityTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class CityController
 * @Route("/admin/city")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class CityController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/city/';

    /**
     * CityController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new CityTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List cities
     * @Route("/", name="city_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param CityDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, CityDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New city
     * @Route("/new", name="city_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new City()), CityType::class);
    }

    /**
     * Show city info
     * @Route("/{id}", name="city_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param City $city
     *
     * @return Response
     * @throws Exception
     */
    public function show(City $city): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $city);
    }

    /**
     * Edit city
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param City $city
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, City $city): Response
    {
        return $this->responseEdit($request, $city, CityType::class);
    }

    /**
     * Delete city
     * @Route("/{id}", name="city_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param City $city
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, City $city): Response
    {
        return $this->responseDelete($request, $city);
    }
}
