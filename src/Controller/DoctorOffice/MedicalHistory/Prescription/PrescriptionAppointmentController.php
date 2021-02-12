<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Form\PrescriptionAppointmentType;
use App\Form\PrescriptionPatientAppointmentType;
use App\Repository\PrescriptionAppointmentRepository;
use App\Services\InfoService\PatientAppointmentInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientAppointmentTemplate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use App\Services\ControllerGetters\EntityActions;

/**
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
     * @Route("doctor_office/patient/{id}/prescription/{prescription}/appointment/new/", name="adding_reception_by_doctor", methods={"GET","POST"})
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param PrescriptionAppointmentRepository $prescriptionAppointmentRepository
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        PrescriptionAppointmentRepository $prescriptionAppointmentRepository
    ): Response
    {
        $patientAppointment = new PatientAppointment();
        $prescriptionAppointment = new PrescriptionAppointment();
        return $this->responseNewMultiForm(
            $request,
            $prescriptionAppointment,
            [
                new FormData($patientAppointment, PrescriptionPatientAppointmentType::class),
                new FormData($prescriptionAppointment, PrescriptionAppointmentType::class),
            ],
            function (EntityActions $entityActions) use (
                $patientAppointment,
                $prescriptionAppointment,
                $prescription,
                $patient,
                $prescriptionAppointmentRepository
            )
            {
                if ((new PatientAppointmentInfoService($this->entityManager))->isAppointmentNotExists($prescription, $prescriptionAppointmentRepository))
                {
                    $patientAppointment
                        ->setMedicalHistory($prescription->getMedicalHistory())
                        ->setStaff($this->getStaff($patient))
                        ->setIsConfirmed(false)
                        ->setEnabled(true)
                        ->setPrescriptionAppointment($prescriptionAppointment)
                        ->setIsFirst(false)
                        ->setIsByPlan(false)
                    ;
                    $prescriptionAppointment
                        ->setPrescription($prescription)
                        ->setPatientAppointment($patientAppointment)
                        ->setStaff($this->getStaff($patient))
                        ->setEnabled(true)
                        ->setInclusionTime(new DateTime())
                        ->setConfirmedByStaff(true)
                    ;
                    $entityActions->getEntityManager()->persist($patientAppointment);
                } else{
                    $this->addFlash(
                        'error',
                        $this->translator->trans(
                            'prescription_appointment_controller.error.new_appointment'
                        )
                    );
                }
            }
        );
    }
}