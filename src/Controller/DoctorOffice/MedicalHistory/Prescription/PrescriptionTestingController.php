<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\PatientTesting\PatientTestingRequiredType;
use App\Form\PrescriptionTestingType;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionTestingCreatorService;
use App\Services\EntityActions\Creator\PatientTestingCreatorService;
use App\Services\EntityActions\Creator\PrescriptionTestingCreatorService;
use App\Services\EntityActions\Creator\SpecialPatientTestingCreatorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/doctor_office")
 * Class AddingSurveyController
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionTestingController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/prescription_testing/';

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
     * @param DoctorOfficePrescriptionTestingCreatorService $prescriptionTestingCreatorService
     * @param SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionTestingCreatorService $prescriptionTestingCreatorService,
        SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
    ): Response
    {
        $patientTesting = $specialPatientTestingCreatorService->execute(
            [
                PatientTestingCreatorService::MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
            ]
        )->getEntity();

        $prescriptionTestingCreatorService->before([
            PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription,
            PrescriptionTestingCreatorService::PATIENT_TESTING_OPTION => $patientTesting
        ]);
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
                new CreatorEntityActionsBuilder(
                    $prescriptionTestingCreatorService,
                    [
                        PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription,
                    ],
                    function () use (
                        $patientTesting,
                        $patient
                    ): array {
                        return [
                            DoctorOfficePrescriptionTestingCreatorService::STAFF_OPTION => $this->getStaff($patient),
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
     * Edit prescription testing
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/testing/{prescriptionTesting}/edit/",
     *     name="edit_prescription_testing_by_doctor",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"})
     *     )
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     * @return Response
     * @throws Exception
     */
    public function edit(
        Request $request,
        PrescriptionTesting $prescriptionTesting
    ): Response
    {
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $prescriptionTesting->getPrescription()->getMedicalHistory()->getPatient(),
                'prescription' => $prescriptionTesting->getPrescription()
            ]
        );
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

    /**
     * Show prescription testing
     * @Route(
     *     "/prescriptionTesting/{patient}/prescription/{prescription}/testing/{prescriptionTesting}/show",
     *     name="show_prescription_testing_by_doctor",
     *     )
     * @param PrescriptionTesting $prescriptionTesting
     * @return Response
     * @throws Exception
     */
    public function show(
        PrescriptionTesting $prescriptionTesting
    ): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $prescriptionTesting, [
                'staffTitle' =>
                    AuthUserInfoService::getFIO($prescriptionTesting->getStaff()->getAuthUser()),
                'backRouteName' => 'add_prescription_show',
                'editRouteName' => 'edit_prescription_testing_by_doctor',
                'deleteRouteName' => 'delete_prescription_testing_by_doctor',
                'routParam' => [
                    'patient' => $prescriptionTesting->getPatientTesting()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $prescriptionTesting->getPrescription()->getId(),
                    'prescriptionTesting' => $prescriptionTesting->getId()
                ]
            ]
        );
    }

    /**
     * Delete prescription testing
     * @Route(
     *     "/prescriptionTesting/{patient}/prescription/{prescription}/testing/{prescriptionTesting}/delete",
     *     name="delete_prescription_testing_by_doctor",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     * @param Patient $patient
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function delete(
        Request $request,
        PrescriptionTesting $prescriptionTesting,
        Patient $patient,
        Prescription $prescription
    ): Response
    {
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $patient->getId(),
                'prescription' => $prescription->getId(),
            ]
        );

        return $this->responseDelete($request, $prescriptionTesting);
    }
}