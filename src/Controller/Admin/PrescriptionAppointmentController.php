<?php

namespace App\Controller\Admin;

use App\Entity\PrescriptionAppointment;
use App\Form\Admin\PrescriptionAppointment\PrescriptionAppointmentInclusionTimeType;
use App\Form\PatientAppointmentType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentConfirmedEnabledType;
use App\Form\Admin\PrescriptionAppointment\PrescriptionAppointmentPlannedDateType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentStaffType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionAppointmentDataTableService;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PrescriptionAppointmentCreatorService;
use App\Services\EntityActions\Creator\SpecialPatientAppointmentCreatorService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientAppointmentInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionAppointmentTemplate;
use Exception;
use ReflectionException;
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
     * @param PrescriptionAppointmentCreatorService $prescriptionAppointmentCreator
     * @param SpecialPatientAppointmentCreatorService $patientAppointmentCreator
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        PrescriptionAppointmentCreatorService $prescriptionAppointmentCreator,
        SpecialPatientAppointmentCreatorService $patientAppointmentCreator
    ): Response
    {
        if (!$prescription = $this->getPrescriptionByParameter($request)) {
            return $this->redirectToRoute('prescription_appointment_list');
        }

        if (PrescriptionInfoService::isPrescriptionAppointmentExists($prescription)) {
            return $this->redirectToRoute(
                'prescription_show',
                [
                    'id' => $prescription->getId(),
                ]
            );
        }

        $patientAppointment = $patientAppointmentCreator->before(
            [
                PatientAppointmentCreatorService::MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
            ]
        )->getEntity();
        $prescriptionAppointment = $prescriptionAppointmentCreator->before(
            [
                PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
                PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
            ]
        )->getEntity();
        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($prescriptionAppointmentCreator),
                new CreatorEntityActionsBuilder(
                    $patientAppointmentCreator,
                    [],
                    function () use ($prescriptionAppointment) {
                        return [
                            //до отправки формы prescriptionAppointment добавить через creator не получается, добавляем после отправки
                            SpecialPatientAppointmentCreatorService::PRESCRIPTION_APPOINTMENT_OPTION => $prescriptionAppointment,
                            SpecialPatientAppointmentCreatorService::STAFF_OPTION => $prescriptionAppointment->getStaff(),
                        ];
                    }
                ),
            ],
            [
                new FormData(PrescriptionAppointmentStaffType::class, $prescriptionAppointment),
                new FormData(PrescriptionAppointmentPlannedDateType::class, $prescriptionAppointment),
                new FormData(PatientAppointmentType::class, $patientAppointment)
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
    public function edit(
        Request $request,
        PrescriptionAppointment $prescriptionAppointment
    ): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $prescriptionAppointment,
            [
                new FormData(PrescriptionAppointmentStaffType::class, $prescriptionAppointment),
                new FormData(PrescriptionAppointmentInclusionTimeType::class, $prescriptionAppointment),
                new FormData(PrescriptionAppointmentPlannedDateType::class, $prescriptionAppointment),
                new FormData(PatientAppointmentType::class, $prescriptionAppointment->getPatientAppointment()),
                new FormData(PrescriptionAppointmentConfirmedEnabledType::class, $prescriptionAppointment),
            ]
        );
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
