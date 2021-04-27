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
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionAppointmentService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PrescriptionAppointmentCreatorService;
use App\Services\EntityActions\Creator\SpecialPatientAppointmentCreatorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientAppointmentTemplate;
use Exception;
use ReflectionException;
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
    const TEMPLATE_PATH = 'doctorOffice/prescription_appointment/';

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
     *     "/patient/{patient}/prescription/{prescription}/appointment/new/",
     *     name="adding_reception_by_doctor",
     *     methods={"GET","POST"})
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreator
     * @param SpecialPatientAppointmentCreatorService $patientAppointmentCreator
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreator,
        SpecialPatientAppointmentCreatorService $patientAppointmentCreator
    ): Response
    {
        $countPrescriptionAppointment = $this->getDoctrine()
            ->getRepository(PrescriptionAppointment::class)
            ->countPrescriptionAppointmentsByPrescription($prescription);
        if ($countPrescriptionAppointment !== 0) {
            return $this->redirectToRoute('add_prescription_show',
                [
                    "patient" => $patient->getId(),
                    "prescription" => $prescription->getId(),
                ]
            );
        }
        $staff = $this->getStaff($patient);
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $patientAppointmentCreator->before(
            [
                PatientAppointmentCreatorService::MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
                SpecialPatientAppointmentCreatorService::STAFF_OPTION => $staff,
            ]
        )->getEntity();
        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $prescriptionAppointmentCreator->before(
            [
                PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION => $prescription,
                PrescriptionAppointmentCreatorService::STAFF_OPTION => $staff,
                PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
            ]
        )->getEntity();

        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $patient,
                'prescription' => $prescription
            ]
        );

        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($prescriptionAppointmentCreator),
                new CreatorEntityActionsBuilder(
                    $patientAppointmentCreator,
                    [],
                    function () use ($prescriptionAppointment) {
                        return [
                            SpecialPatientAppointmentCreatorService::PRESCRIPTION_APPOINTMENT_OPTION => $prescriptionAppointment,
                        ];
                    }
                ),
            ],
            [
                new FormData(PrescriptionAppointmentPlannedDateType::class, $prescriptionAppointment),
                new FormData(PatientAppointmentType::class, $patientAppointment),
            ]
        );
    }

    /**
     * Show prescription testing
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/show/",
     *     name="show_prescription_appointment_by_doctor",
     *     )
     * @param PrescriptionAppointment $prescriptionAppointment
     * @return Response
     * @throws Exception
     */
    public function show(
        PrescriptionAppointment $prescriptionAppointment
    ): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $prescriptionAppointment, [
                'staffTitle' =>
                    AuthUserInfoService::getFIO($prescriptionAppointment->getStaff()->getAuthUser()),
                'backRouteName' => 'add_prescription_show',
                'editRouteName' => 'edit_prescription_testing_by_doctor',
                'deleteRouteName' => 'delete_prescription_testing_by_doctor',
                'routParam' => [
                    'patient' => $prescriptionAppointment->getPatientAppointment()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $prescriptionAppointment->getPrescription()->getId(),
                    'prescriptionTesting' => $prescriptionAppointment->getId()
                ]
            ]
        );
    }

    /**
     * Edit prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/edit/",
     *     name="edit_prescription_appointment_by_doctor",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "prescription"="\d+"}
     *     )
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
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $prescriptionAppointment->getPrescription()->getMedicalHistory()->getPatient(),
                'prescription' => $prescriptionAppointment->getPrescription()
            ]
        );

        return $this->responseEditMultiForm(
            $request,
            $prescriptionAppointment,
            [
                new FormData(PrescriptionAppointmentPlannedDateType::class, $prescriptionAppointment),
                new FormData(PatientAppointmentType::class, $prescriptionAppointment->getPatientAppointment()),
            ]
        );
    }

    /**
     * Delete prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/delete",
     *     name="delete_prescription_appointment_by_doctor",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param PrescriptionAppointment $prescriptionAppointment
     * @param Patient $patient
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function delete(
        Request $request,
        PrescriptionAppointment $prescriptionAppointment,
        Patient $patient,
        Prescription $prescription
    ): Response
    {
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $patient->getId(),
                'prescription' => $prescription->getId()
            ]
        );
        return $this->responseDelete($request, $prescriptionAppointment);
    }
}