<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\Admin\CountryType;
use App\Services\DataTable\Admin\CountryDataTableService;
use App\Services\TemplateBuilders\Admin\CountryTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class CountryController
 * @Route("/admin/country")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class CountryController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/country/';

    /**
     * CountryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new CountryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Country list
     * @Route("/", name="country_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param CountryDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, CountryDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New country
     * @Route("/new", name="country_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Country()), CountryType::class);
    }

    /**
     * Show country info
     * @Route("/{id}", name="country_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Country $country
     *
     * @return Response
     */
    public function show(Country $country): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $country);
    }

    /**
     * Edit country
     * @Route("/{id}/edit", name="country_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Country $country
     *
     * @return Response
     */
    public function edit(Request $request, Country $country): Response
    {
        return $this->responseEdit($request, $country, CountryType::class);
    }

    /**
     * Delete country
     * @Route("/{id}", name="country_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Country $country
     *
     * @return Response
     */
    public function delete(Request $request, Country $country): Response
    {
        return $this->responseDelete($request, $country);
    }
}
