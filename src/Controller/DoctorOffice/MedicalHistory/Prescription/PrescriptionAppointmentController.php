<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\PatientAppointmentType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentPlannedDateType;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionAppointmentService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PrescriptionAppointmentCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientAppointmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @Route("/doctor_office")
 * Class AddingReceptionController
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionAppointmentController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctor_office/common_template/';

    /**
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->templateService = new PatientAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * New prescription appointment
     * @Route(
     *     "doctor_office/patient/{patient}/prescription/{prescription}/appointment/new/",
     *     name="adding_reception_by_doctor",
     *     methods={"GET","POST"})
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreatorService
     * @param DoctorOfficePatientAppointmentCreatorService $patientAppointmentCreatorService
     * @return Response
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreatorService,
        DoctorOfficePatientAppointmentCreatorService $patientAppointmentCreatorService
    ): Response
    {
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $patientAppointmentCreatorService->before(
            [
                PatientAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
                PatientAppointmentCreatorService::STAFF_OPTION => $this->getStaff($patient),
            ]
        )->getEntity();
        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $prescriptionAppointmentCreatorService->before([
            PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
            PrescriptionAppointmentCreatorService::STAFF_OPTION => $this->getStaff($patient),
            PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
        ])->getEntity();

        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($prescriptionAppointmentCreatorService),
                new CreatorEntityActionsBuilder(
                    $patientAppointmentCreatorService,
                    [],
                    function () use ($prescriptionAppointment) {
                        return [
                            PatientAppointmentCreatorService::PRESCRIPTION_APPOINTMENT_OPTION => $prescriptionAppointment
                        ];
                    }
                ),
            ],
            [
                new FormData(
                    PrescriptionAppointmentPlannedDateType::class,
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
     * Edit prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/testing/{prescriptionAppointment}/edit/",
     *     name="edit_prescription_appointment_by_doctor",
     *     methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     * @throws \Exception
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
                new FormData(
                    PrescriptionAppointmentPlannedDateType::class,
                    $prescriptionAppointment
                ),
                new FormData(
                    PatientAppointmentType::class,
                    $prescriptionAppointment->getPatientAppointment()
                )
            ]
        );
    }
}