<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\Admin\CountryType;
use App\Services\DataTable\Admin\CountryDataTableService;
use App\Services\TemplateBuilders\Admin\CountryTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
        $this->templateService = new CountryTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function edit(Request $request, Country $country): Response
    {
        return $this->responseEdit($request, $country, CountryType::class);
    }

    /**
     * Delete country
     * @Route("/{id}", name="country_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Country $country
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Country $country): Response
    {
        return $this->responseDelete($request, $country);
    }
}
