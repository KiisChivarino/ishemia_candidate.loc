<?php

namespace App\Controller\DoctorOffice;

use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\Patient\PatientClinicalDiagnosisTextType;
use App\Form\Patient\PatientLocationRequiredType;
use App\Form\Patient\PatientMKBCodeType;
use App\Form\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\ByDoctorFirstPatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\ByDoctorHospitalPatientCreatorService;
use App\Services\EntityActions\Creator\PatientCreatorService;
use App\Services\EntityActions\Factory\AbstractCreatingPatientServicesFactory;
use App\Services\EntityActions\Factory\ByDoctorConsultantCreatingPatientServicesFactory;
use App\Services\EntityActions\Factory\ByDoctorHospitalCreatingPatientServicesFactory;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class MedicalHistoryController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class AddPatientController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/create_patient/';

    /**
     * PatientsListController constructor.
     *
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
        $this->templateService = new CreateNewPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Add new patient by doctor of hospital
     * @Route("/create_patient_by_hospital_doctor", name="adding_patient_by_hospital_doctor", methods={"GET","POST"})
     * @param Request $request
     * @param ByDoctorHospitalCreatingPatientServicesFactory $patientCreatingFactory
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function createNewByDoctorHospital(
        Request $request,
        ByDoctorHospitalCreatingPatientServicesFactory $patientCreatingFactory
    )
    {
        $clinicalDiagnosis = $patientCreatingFactory->getClinicalDiagnosis();
        $response = $this->responseNewMultiFormWithActions(
            $request,
            $this->getCreatorEntityActionsBuilderArray($patientCreatingFactory),
            [
                new FormData(AuthUserRequiredType::class, $patientCreatingFactory->getAuthUser()),
                new FormData(PatientRequiredType::class, $patientCreatingFactory->getPatient()),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(AppointmentTypeType::class, $patientCreatingFactory->getPatientAppointment()),
            ]
        );
        $this->templateService->setRedirectRoute(
            self::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [
                'id' => $patientCreatingFactory->getPatient()->getId(),
            ]
        );
        return $response;
    }

    /**
     * Add new patient by doctor consultant
     * @Route("/create_patient_by_doctor_consultant", name="adding_patient_by_doctor_consultant", methods={"GET","POST"})
     *
     * @param Request $request
     * @param ByDoctorConsultantCreatingPatientServicesFactory $patientCreatingFactory
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function createNewByDoctorConsultant(
        Request $request,
        ByDoctorConsultantCreatingPatientServicesFactory $patientCreatingFactory
    ): Response
    {
        $clinicalDiagnosis = $patientCreatingFactory->getClinicalDiagnosis();
        $patient = $patientCreatingFactory->getPatient();
        $response = $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($patientCreatingFactory->getAuthUserCreator()),
                new CreatorEntityActionsBuilder($patientCreatingFactory->getPatientCreator()),
                new CreatorEntityActionsBuilder($patientCreatingFactory->getMedicalHistoryCreator()),
                new CreatorEntityActionsBuilder(
                    $patientCreatingFactory->getPatientAppointmentCreator(),
                    [],
                    function ()
                    use ($patientCreatingFactory) {
                        return
                            [
                                ByDoctorFirstPatientAppointmentCreatorService::STAFF_OPTION =>
                                    $this->getStaff($patientCreatingFactory->getPatientCreator()->getEntity())
                            ];
                    }
                ),
            ],
            [
                new FormData(AuthUserRequiredType::class, $patientCreatingFactory->getAuthUser()),
                new FormData(PatientRequiredType::class, $patient),
                new FormData(PatientLocationRequiredType::class, $patient),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(AppointmentTypeType::class, $patientCreatingFactory->getPatientAppointment()),
            ]
        );
        $this->templateService->setRedirectRoute(
            self::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [
                'id' => $patientCreatingFactory->getPatient()->getId(),
            ]
        );
        return $response;
    }

    /**
     * @param AbstractCreatingPatientServicesFactory $patientCreatingFactory
     * @return CreatorEntityActionsBuilder[]
     */
    private function getCreatorEntityActionsBuilderArray(AbstractCreatingPatientServicesFactory $patientCreatingFactory): array
    {
        return [
            new CreatorEntityActionsBuilder($patientCreatingFactory->getAuthUserCreator()),
            new CreatorEntityActionsBuilder(
                $patientCreatingFactory->getPatientCreator(),
                [],
                function (PatientCreatorService $byDoctorHospitalPatientCreator) {
                    return
                        [
                            ByDoctorHospitalPatientCreatorService::STAFF_OPTION =>
                                $this->getStaff($byDoctorHospitalPatientCreator->getEntity())
                        ];
                }
            ),
            new CreatorEntityActionsBuilder($patientCreatingFactory->getMedicalHistoryCreator()),
            new CreatorEntityActionsBuilder(
                $patientCreatingFactory->getPatientAppointmentCreator(),
                [],
                function ()
                use ($patientCreatingFactory) {
                    return
                        [
                            ByDoctorFirstPatientAppointmentCreatorService::STAFF_OPTION =>
                                $this->getStaff($patientCreatingFactory->getPatientCreator()->getEntity())
                        ];
                }
            ),
        ];
    }
}