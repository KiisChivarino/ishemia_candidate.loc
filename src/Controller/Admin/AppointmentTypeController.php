<?php

namespace App\Controller\Admin;

use App\Entity\AppointmentType;
use App\Form\Admin\AppointmentTypeType;
use App\Services\DataTable\Admin\AppointmentTypeDataTableService;
use App\Services\TemplateBuilders\Admin\AppointmentTypeTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AppointmentTypeController
 * @Route("/admin/appointment_type")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class AppointmentTypeController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/appointment_type/';

    /**
     * AppointmentTypeController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new AppointmentTypeTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of appointment types
     * @Route("/", name="appointment_type_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AppointmentTypeDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, AppointmentTypeDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New appointment type
     * @Route("/new", name="appointment_type_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new AppointmentType()), AppointmentTypeType::class);
    }

    /**
     * Show appointment type
     * @Route("/{id}", name="appointment_type_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param AppointmentType $appointmentType
     *
     * @return Response
     * @throws Exception
     */
    public function show(AppointmentType $appointmentType): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $appointmentType);
    }

    /**
     * Edit appointment type
     * @Route("/{id}/edit", name="appointment_type_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AppointmentType $appointmentType
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, AppointmentType $appointmentType): Response
    {
        return $this->responseEdit($request, $appointmentType, AppointmentTypeType::class);
    }

    /**
     * Delete appointment type
     * @Route("/{id}", name="appointment_type_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AppointmentType $appointmentType
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, AppointmentType $appointmentType): Response
    {
        return $this->responseDelete($request, $appointmentType);
    }
}
