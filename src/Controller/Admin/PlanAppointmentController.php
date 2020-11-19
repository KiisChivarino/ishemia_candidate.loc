<?php

namespace App\Controller\Admin;

use App\Entity\PlanAppointment;
use App\Form\Admin\PlanAppointmentType;
use App\Services\DataTable\Admin\PlanAppointmentDataTableService;
use App\Services\TemplateBuilders\Admin\PlanAppointmentTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PlanAppointmentController
 * @Route("/admin/plan_appointment")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PlanAppointmentController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/plan_appointment/';

    /**
     * PlanAppointmentController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PlanAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Plan appointment list
     * @Route("/", name="plan_appointment_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PlanAppointmentDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, PlanAppointmentDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New plan appointment
     * @Route("/new", name="plan_appointment_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new PlanAppointment()), PlanAppointmentType::class);
    }

    /**
     * Show plan appointment
     * @Route("/{id}", name="plan_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PlanAppointment $planAppointment
     *
     * @return Response
     */
    public function show(PlanAppointment $planAppointment): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $planAppointment);
    }

    /**
     * @Route("/{id}/edit", name="plan_appointment_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PlanAppointment $planAppointment
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PlanAppointment $planAppointment): Response
    {
        return $this->responseEdit($request, $planAppointment, PlanAppointmentType::class);
    }

    /**
     * @Route("/{id}", name="plan_appointment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PlanAppointment $planAppointment
     *
     * @return Response
     */
    public function delete(Request $request, PlanAppointment $planAppointment): Response
    {
        return $this->responseDelete($request, $planAppointment);
    }
}
