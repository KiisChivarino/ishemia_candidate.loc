<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Form\Admin\RoleType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\DataTable\Admin\RoleDataTableService;
use App\Services\TemplateBuilders\RoleTemplate;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class RoleController
 * @Route("/admin/role")
 *
 * @package App\Controller\Admin
 */
class RoleController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/role/';

    /**
     * RoleController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new RoleTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of roles
     * @Route("/", name="role_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param RoleDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, RoleDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Show role info
     * @Route("/{id}", name="role_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Role $role
     *
     * @return Response
     */
    public function show(Role $role): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $role);
    }

    /**
     * Edit role
     * @Route("/{id}/edit", name="role_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Role $role
     *
     * @return Response
     */
    public function edit(Request $request, Role $role): Response
    {
        return $this->responseEdit($request, $role, RoleType::class, [], $this->setTechNameFunction());
    }

    /**
     * Set tech name for role
     * @return Closure
     */
    public function setTechNameFunction(): Closure
    {
        return function (EntityActions $actions) {
            return $actions->getEntity()->setTechName($actions->getEntity()->getTechName());
        };
    }
}
