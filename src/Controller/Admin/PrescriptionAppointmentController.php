<?php

namespace App\Controller\Admin;

use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\Admin\Prescription\PrescriptionAppointmentType;
use App\Form\PatientAppointmentType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentPlannedDateType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentStaffType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionAppointmentDataTableService;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionAppointmentService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PrescriptionAppointmentCreatorService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientAppointmentInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionAppointmentTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreatorService
     * @param PatientAppointmentCreatorService $patientAppointmentCreatorService
     * @return Response
     * @throws \ReflectionException
     */
    public function new(
        Request $request,
        PrescriptionAppointmentCreatorService $prescriptionAppointmentCreatorService,
        PatientAppointmentCreatorService $patientAppointmentCreatorService
    ): Response
    {
        $prescription = $this->getPrescriptionByParameter($request);

        $patientAppointment = $patientAppointmentCreatorService->execute(
            [
                PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription
            ])->getEntity();

        $prescriptionAppointmentCreatorService->before([
            PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
            PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
        ]);


        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder(
                    $prescriptionAppointmentCreatorService,
                    [
                        PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
                    ],
                    function (PrescriptionAppointmentCreatorService $prescriptionAppointmentCreatorService) use (
                        $patientAppointment,
                        $prescription,
                        $patientAppointmentCreatorService
                    ): array {
                        return [
                            PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
                        ];
                    }
                )
            ],
            [
                new FormData(
                    PrescriptionAppointmentPlannedDateType::class,
                    $prescriptionAppointmentCreatorService->getEntity()
                ),
                new FormData(
                    PrescriptionAppointmentStaffType::class,
                    $prescriptionAppointmentCreatorService->getEntity()
                ),
                new FormData(
                    PatientAppointmentType::class,
                    $patientAppointmentCreatorService->getEntity()
                )
            ]
        );
    }

    /**
     * Show appointment prescription
     * @Route("/{id}", name="prescription_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     * @throws Exception
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
     * @throws Exception
     */
    public function delete(Request $request, PrescriptionAppointment $prescriptionAppointment): Response
    {
        return $this->responseDelete($request, $prescriptionAppointment);
    }
}
