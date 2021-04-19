<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Form\Admin\PrescriptionMedicineType;
use App\Form\Admin\PrescriptionMedicineTypeEnabled;
use App\Form\Doctor\PatientMedicineType;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionMedicineCreatorService;
use App\Services\EntityActions\Creator\PatientMedicineCreatorService;
use App\Services\EntityActions\Creator\PrescriptionMedicineCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionMedicineTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AddingMedicationController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionMedicineController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/patient_medicine/';

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $STAFF_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRIPTION_OPTION;

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
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     * @param string $staffOption
     * @param string $prescriptionOption
     * @param string $medicalHistoryOption
     * @param string $prescriptionMedicine
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        PatientMedicineCreatorService $patientMedicineCreatorService,
        string $staffOption,
        string $prescriptionOption,
        string $medicalHistoryOption,
        string $prescriptionMedicine
    )
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->creatorService = $patientMedicineCreatorService;
        $this->STAFF_OPTION = $staffOption;
        $this->PRESCRIPTION_OPTION = $prescriptionOption;
        $this->MEDICAL_HISTORY_OPTION = $medicalHistoryOption;
        $this->PRESCRIPTION_MEDICINE = $prescriptionMedicine;
    }

    /**
     * New medicine prescription
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/patient_medicine/new",
     *     name="prescription_patient_medicine_new",
     *     methods={"GET","POST"},
     *     requirements={"prescription"="\d+"}
     *     )
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionMedicineCreatorService $prescriptionMedicineCreatorService
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     * @return Response
     * @throws \ReflectionException
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionMedicineCreatorService $prescriptionMedicineCreatorService,
        PatientMedicineCreatorService $patientMedicineCreatorService
    ): Response
    {
        $patientMedicine = $patientMedicineCreatorService->execute(
            [
                PrescriptionMedicineCreatorService::PRESCRIPTION_OPTION => $prescription
            ]
        )->getEntity();

        $prescriptionMedicineCreatorService->before([
            PrescriptionMedicineCreatorService::PRESCRIPTION_OPTION => $prescription,
            PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine
        ]);

        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder(
                    $prescriptionMedicineCreatorService,
                    [
                        PrescriptionMedicineCreatorService::PRESCRIPTION_OPTION => $prescription,
                    ],
                    function (PrescriptionMedicineCreatorService $prescriptionMedicineCreatorService) use (
                        $patientMedicine,
                        $prescription,
                        $patientMedicineCreatorService,
                        $patient
                    ): array {
                        return [
                            PrescriptionMedicineCreatorService::STAFF_OPTION => $this->getStaff($patient),
                            PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine,
                        ];
                    }
                )
            ],
            [
                new FormData(
                    PrescriptionMedicineType::class,
                    $prescriptionMedicineCreatorService->getEntity()
                ),
                new FormData(
                    PrescriptionMedicineTypeEnabled::class,
                    $prescriptionMedicineCreatorService->getEntity()
                ),
                new FormData(
                    PatientMedicineType::class,
                    $patientMedicineCreatorService->getEntity()
                ),
            ]
        );
    }

    /**
     * Edit prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/patient_medicine/{prescriptionTesting}/edit/",
     *     name="edit_patient_medicine_by_doctor",
     *     methods={"GET","POST"}
     *     )
     * @return Response
     */
    public function edit(

    ): Response
    {
    }
}