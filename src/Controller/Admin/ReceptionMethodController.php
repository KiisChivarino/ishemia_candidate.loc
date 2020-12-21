<?php

namespace App\Controller\Admin;

use App\Entity\ReceptionMethod;
use App\Form\Admin\ReceptionMethodType;
use App\Services\DataTable\Admin\ReceptionMethodDataTableService;
use App\Services\TemplateBuilders\Admin\ReceptionMethodTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ReceptionMethodController
 * @Route("/admin/reception_method")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class ReceptionMethodController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/reception_method/';

    /**
     * ReceptionMethodController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new ReceptionMethodTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Reception method list
     * @Route("/", name="reception_method_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param ReceptionMethodDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, ReceptionMethodDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New reception method
     * @Route("/new", name="reception_method_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new ReceptionMethod()), ReceptionMethodType::class);
    }

    /**
     * Show reception method
     * @Route("/{id}", name="reception_method_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param ReceptionMethod $receptionMethod
     *
     * @return Response
     * @throws Exception
     */
    public function show(ReceptionMethod $receptionMethod): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $receptionMethod);
    }

    /**
     * Edit reception method
     * @Route("/{id}/edit", name="reception_method_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param ReceptionMethod $receptionMethod
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, ReceptionMethod $receptionMethod): Response
    {
        return $this->responseEdit($request, $receptionMethod, ReceptionMethodType::class);
    }

    /**
     * Delete reception method
     * @Route("/{id}", name="reception_method_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param ReceptionMethod $receptionMethod
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, ReceptionMethod $receptionMethod): Response
    {
        return $this->responseDelete($request, $receptionMethod);
    }
}
