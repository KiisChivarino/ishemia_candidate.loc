<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\PatientAppointmentType;
use App\Form\Doctor\PrescriptionAppointment\PrescriptionAppointmentPlannedDateType;
use App\Repository\PrescriptionAppointmentRepository;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
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
 *
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionAppointmentController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/prescription_appointment/';

    /**
     * PatientPrescriptionController constructor.
     *
     * @param Environment         $twig
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
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
     *
     * @param Request                                    $request
     * @param Prescription                               $prescription
     * @param Patient                                    $patient
     * @param DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreator
     * @param SpecialPatientAppointmentCreatorService    $patientAppointmentCreator
     * @param PrescriptionAppointmentRepository          $prescriptionAppointmentRepository
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreator,
        SpecialPatientAppointmentCreatorService $patientAppointmentCreator,
        PrescriptionAppointmentRepository $prescriptionAppointmentRepository
    ): Response {
        $this->setRedirectRouteToNewPrescription($patient, $prescription);
        $countPrescriptionAppointment = $prescriptionAppointmentRepository
            ->countPrescriptionAppointmentsByPrescription($prescription);
        if ($countPrescriptionAppointment !== 0) {
            return
                $this->redirectToRoute(
                    $this->templateService->getRedirectRouteName(),
                    $this->templateService->getRedirectRouteParameters()
                );
        }
        if ($prescription->getIsCompleted()) {
            return $this->redirectToMedicalHistory($patient);
        }

        $staff = $this->getStaff($patient);
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $patientAppointmentCreator->before(
            [
                PatientAppointmentCreatorService::MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
                SpecialPatientAppointmentCreatorService::STAFF_OPTION    => $staff,
            ]
        )->getEntity();

        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $prescriptionAppointmentCreator->before(
            [
                PrescriptionAppointmentCreatorService::PRESCRIPTION_OPTION        => $prescription,
                PrescriptionAppointmentCreatorService::STAFF_OPTION               => $staff,
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
                            SpecialPatientAppointmentCreatorService::PRESCRIPTION_APPOINTMENT_OPTION
                            => $prescriptionAppointment,
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
     * Edit prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/edit/",
     *     name="edit_prescription_appointment_by_doctor",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "prescription"="\d+"}
     *     )
     *
     * @param Request                 $request
     * @param PrescriptionAppointment $prescriptionAppointment
     *
     * @return Response
     * @throws Exception
     */
    public function edit(
        Request $request,
        PrescriptionAppointment $prescriptionAppointment
    ): Response {
        $prescription = $prescriptionAppointment->getPrescription();
        $patient = $prescription->getMedicalHistory()->getPatient();
        if ($prescriptionAppointment->getPrescription()->getIsCompleted()) {
            return $this->redirectToMedicalHistory($patient);
        }
        $this->setRedirectRouteToNewPrescription($patient, $prescription);

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
     * Show prescription testing
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/show/",
     *     name="show_prescription_appointment_by_doctor",
     *     )
     *
     * @param PrescriptionAppointment $prescriptionAppointment
     *
     * @return Response
     * @throws Exception
     */
    public function show(
        PrescriptionAppointment $prescriptionAppointment
    ): Response {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $prescriptionAppointment, [
                'staffTitle' =>
                    AuthUserInfoService::getFIO($prescriptionAppointment->getStaff()->getAuthUser())
            ]
        );
    }

    /**
     * Delete prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/appointment/{prescriptionAppointment}/delete",
     *     name="delete_prescription_appointment_by_doctor",
     *     methods={"DELETE"},
     *     )
     *
     * @param Request                 $request
     * @param PrescriptionAppointment $prescriptionAppointment
     * @param Patient                 $patient
     * @param Prescription            $prescription
     *
     * @return Response
     * @throws Exception
     */
    public function delete(
        Request $request,
        PrescriptionAppointment $prescriptionAppointment,
        Patient $patient,
        Prescription $prescription
    ): Response {
        $this->setRedirectRouteToNewPrescription($patient, $prescription);

        return $this->responseDelete($request, $prescriptionAppointment);
    }

}