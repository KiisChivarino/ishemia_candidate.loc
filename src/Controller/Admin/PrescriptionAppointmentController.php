<?php

namespace App\Controller\Admin;

use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\PrescriptionAppointmentType;
use App\Repository\PrescriptionRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionAppointmentDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientAppointmentInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionAppointmentTemplate;
use DateTime;
use Exception;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PrescriptionAppointmentController
 * @Route("/admin/prescription_appointment")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PrescriptionAppointmentController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/prescription_appointment/';

    /**
     * PrescriptionAppointmentController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PrescriptionAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of prescription appointments
     * @Route("/", name="prescription_appointment_list", methods={"GET","POST"})
     * @param Request $request
     * @param PrescriptionAppointmentDataTableService $dataTableService
     * @param FilterService $filterService
     * @return Response
     */
    public function list(
        Request $request,
        PrescriptionAppointmentDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['PRESCRIPTION'],
                ]
            )
        );
    }

    /**
     * New patient appointment
     * @Route("/new", name="prescription_appointment_new", methods={"GET","POST"})
     * @param Request $request
     * @param PrescriptionRepository $prescriptionRepository
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(Request $request, PrescriptionRepository $prescriptionRepository): Response
    {
        if ($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)) {
            /** @var Prescription $prescription */
            $prescription = $prescriptionRepository
                ->find($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY));
            if (!$prescription || !is_a($prescription, Prescription::class)) {
                $this->addFlash(
                    'warning',
                    'Назначение на прием не может быть добавлено: назначение не найдено!'
                );
                return $this->redirectToRoute($this->templateService->getRoute('new'));
            } else {
                $patientAppointment = (new PatientAppointment())
                    ->setEnabled(true)
                    ->setMedicalHistory($prescription->getMedicalHistory());
                $prescriptionAppointment = (new PrescriptionAppointment())
                    ->setPrescription($prescription)
                    ->setEnabled(true)
                    ->setPatientAppointment($patientAppointment)
                    ->setInclusionTime(new DateTime());
                return $this->responseNewMultiForm(
                    $request,
                    $prescriptionAppointment,
                    [
                        new FormData($prescriptionAppointment, PrescriptionAppointmentType::class),
                    ]
                );
            }
        } else {
            $this->addFlash(
                'warning',
                'Назначение на прием не может быть добавлено: необходим код назначения!'
            );
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
    }

    /**
     * Show appointment prescription
     * @Route("/{id}", name="prescription_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     */
    public function show(PrescriptionAppointment $prescriptionAppointment): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescriptionAppointment, [
                'prescriptionTitle' =>
                    PrescriptionInfoService::getPrescriptionTitle($prescriptionAppointment->getPrescription()),
                'patientAppointmentInfo' =>
                    PatientAppointmentInfoService::getPatientAppointmentInfoString($prescriptionAppointment->getPatientAppointment()),
                'staff' =>
                    AuthUserInfoService::getFIO($prescriptionAppointment->getStaff()->getAuthUser(), true),
            ]
        );
    }

    /**
     * Edit prescription appointment
     * @Route("/{id}/edit", name="prescription_appointment_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PrescriptionAppointment $prescriptionAppointment): Response
    {
        return $this->responseEdit($request, $prescriptionAppointment, PrescriptionAppointmentType::class);
    }

    /**
     * Delete prescription appointment
     * @Route("/{id}", name="prescription_appointment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     */
    public function delete(Request $request, PrescriptionAppointment $prescriptionAppointment): Response
    {
        return $this->responseDelete($request, $prescriptionAppointment);
    }
}