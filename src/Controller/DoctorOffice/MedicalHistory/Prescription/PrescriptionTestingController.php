<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\PrescriptionTestingExaminationType;
use App\Form\PatientTesting\PatientTestingRequiredType;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingTemplate;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\ControllerGetters\EntityActions;
use DateTime;

/**
 * Class AddingSurveyController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionTestingController extends DoctorOfficeAbstractController
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
        $this->templateService = new PatientTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * New prescription
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/patient_testing/new",
     *     name="adding_testing_by_doctor", methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param Patient $patient
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        Patient $patient,
        Prescription $prescription
    ): Response
    {
        $patientTesting = new PatientTesting();
        $prescriptionTesting = new PrescriptionTesting();
        return $this->responseNewMultiForm(
            $request,
            $patientTesting,
            [
                new FormData($patientTesting, PatientTestingRequiredType::class),
                new FormData($prescriptionTesting, PrescriptionTestingExaminationType::class),
            ],
        function (EntityActions $entityActions) use ($patientTesting, $prescriptionTesting, $prescription, $patient)
            {
                $this->templateService->setRedirectRoute(
                    'add_prescription_show',
                    [
                        'patient' => $patient->getId(),
                        'prescription' => $prescription->getId(),
                    ]
                );
                $patientTesting
                    ->setMedicalHistory($prescription->getMedicalHistory())
                    ->setIsProcessedByStaff(false)
                    ->setEnabled(true)
                    ->setAnalysisDate(null)
                    ->setIsFirst(false)
                    ->setIsByPlan(false);
                $prescriptionTesting
                    ->setStaff($this->getStaff($patient))
                    ->setEnabled(true)
                    ->setInclusionTime(new DateTime())
                    ->setPrescription($prescription)
                    ->setPatientTesting($patientTesting);
                $entityActions->getEntityManager()->persist($prescriptionTesting);
            }
        );
    }
}