<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\PatientTesting\PatientTestingRequiredType;
use App\Form\PrescriptionTestingType;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionTestingService;
use App\Services\EntityActions\Creator\PrescriptionTestingCreatorService;
use App\Services\EntityActions\Creator\SpecialPatientTestingCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingTemplate;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AddingSurveyController
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
     * New prescription testing
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/testing/new",
     *     name="adding_testing_by_doctor", methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionTestingService $prescriptionTestingCreatorService
     * @param SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
     * @return Response
     * @throws \ReflectionException
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionTestingService $prescriptionTestingCreatorService,
        SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
    ): Response
    {
        $patientTesting = $specialPatientTestingCreatorService->execute(
        [
            PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription
        ])->getEntity();

        $prescriptionTestingCreatorService->before([
            PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription,
            PrescriptionTestingCreatorService::PATIENT_TESTING_OPTION => $patientTesting
        ]);

        return $this->responseNewMultiFormWithActions(
            $request,
            [
            new CreatorEntityActionsBuilder(
                $prescriptionTestingCreatorService,
                [
                    PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription,
                ],
                function (PrescriptionTestingCreatorService $prescriptionTestingCreatorService) use (
                    $patientTesting,
                    $prescription,
                    $specialPatientTestingCreatorService,
                    $patient
                ): array {
                    return [
                        PrescriptionTestingCreatorService::STAFF_OPTION => $this->getStaff($patient),
                        PrescriptionTestingCreatorService::PATIENT_TESTING_OPTION => $patientTesting
                    ];
                }
            )
            ],
            [
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingPlannedDateType::class,
                    $prescriptionTestingCreatorService->getEntity()
                ),
                new FormData(PatientTestingRequiredType::class, $patientTesting),
            ]
        );
    }

    /**
     * Edit prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/testing/{prescriptionTesting}/edit/",
     *     name="edit_prescription_testing_by_doctor",
     *     methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     * @return Response
     * @throws \Exception
     */
    public function edit(
        Request $request,
        PrescriptionTesting $prescriptionTesting
    ): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $prescriptionTesting,
            [
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingPlannedDateType::class,
                    $prescriptionTesting
                ),
                new FormData(
                    PatientTestingRequiredType::class,
                    $prescriptionTesting->getPatientTesting()
                ),
            ]
        );
    }
}