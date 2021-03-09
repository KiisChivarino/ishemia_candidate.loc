<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Form\Doctor\PatientMedicineType;
use App\Services\EntityActions\Creator\PatientMedicineCreatorService;
use App\Services\EntityActions\Creator\PrescriptionMedicineCreatorService;
use App\Services\TemplateBuilders\DoctorOffice\PatientMedicineTemplate;
use Exception;
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
        $this->templateService = new PatientMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->creatorService = $patientMedicineCreatorService;
        $this->STAFF_OPTION = $staffOption;
        $this->PRESCRIPTION_OPTION = $prescriptionOption;
        $this->MEDICAL_HISTORY_OPTION = $medicalHistoryOption;
        $this->PRESCRIPTION_MEDICINE = $prescriptionMedicine;
    }

    /**
     * New medicine prescription
     * @Route("/patient/{patient}/prescription/{prescription}/patient_medicine/new", name="prescription_patient_medicine_new", methods={"GET","POST"})
     * @param Request $request
     * @param Prescription $prescription
     * @param PrescriptionMedicineCreatorService $prescriptionMedicineCreatorService
     * @param Patient $patient
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        PrescriptionMedicineCreatorService $prescriptionMedicineCreatorService,
        Patient $patient
    ): Response
    {
        $prescriptionMedicineCreatorService->execute(
            [
                $this->PRESCRIPTION_OPTION => $prescription,
                $this->STAFF_OPTION => $this->getStaff($patient),
            ]
        );
        return $this->responseNewWithActions(
            $request,
            PatientMedicineType::class,
            [
                $this->MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
                $this->PRESCRIPTION_MEDICINE => $prescriptionMedicineCreatorService->getEntity(),
            ]
        );
    }
}