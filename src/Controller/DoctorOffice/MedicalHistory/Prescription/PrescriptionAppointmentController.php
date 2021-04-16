<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\PatientAppointmentType;
use App\Form\PrescriptionAppointmentType;
use App\Form\PrescriptionAppointmentType\PrescriptionAppointmentPlannedDateType;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionAppointmentService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PrescriptionAppointmentCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientAppointmentTemplate;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $STAFF_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRITION_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $MEDICAL_HISTORY_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRIPTION_MEDICINE;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @param string $staffOption
     * @param string $prescriptionOption
     * @param string $medicalHistoryOption
     * @param string $prescriptionMedicine
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        string $staffOption,
        string $prescriptionOption,
        string $medicalHistoryOption,
        string $prescriptionMedicine
    )
    {
        parent::__construct($translator);
        $this->entityManager = $entityManager;
        $this->templateService = new PatientAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->STAFF_OPTION = $staffOption;
        $this->PRESCRITION_OPTION = $prescriptionOption;
        $this->MEDICAL_HISTORY_OPTION = $medicalHistoryOption;
        $this->PRESCRIPTION_MEDICINE = $prescriptionMedicine;
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
     * @param PatientAppointmentCreatorService $patientAppointmentCreatorService
     * @return Response
     * @throws \ReflectionException
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionAppointmentService $prescriptionAppointmentCreatorService,
        PatientAppointmentCreatorService $patientAppointmentCreatorService
    ): Response
    {

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
                        $patientAppointmentCreatorService,
                        $patient
                    ): array {
                        return [
                            PrescriptionAppointmentCreatorService::STAFF_OPTION => $this->getStaff($patient),
                            PrescriptionAppointmentCreatorService::PATIENT_APPOINTMENT_OPTION => $patientAppointment
                        ];
                    }
                )
            ],
            [
                new FormData(
                    PrescriptionAppointmentType\PrescriptionAppointmentPlannedDateType::class,
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