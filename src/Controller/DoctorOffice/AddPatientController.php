<?php

namespace App\Controller\DoctorOffice;

use App\Entity\ClinicalDiagnosis;
use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\Admin\Patient\PatientClinicalDiagnosisTextType;
use App\Form\Admin\Patient\PatientMKBCodeType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Repository\StaffRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\Creator\MedicalHistoryCreatorService;
use App\Services\Creator\PatientAppointmentCreatorService;
use App\Services\Creator\PatientCreatorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Exception;

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

    /** @var string array key name */
    const IS_DOCTOR_HOSPITAL = 'isDoctorHospital';

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
     * Add newPatient
     * @Route("/create_patient", name="adding_patient_by_doctor", methods={"GET","POST"})
     *
     * @param Request $request
     * @param StaffRepository $staffRepository
     * @param AuthUserCreatorService $authUserCreatorService
     * @param MedicalHistoryCreatorService $medicalHistoryCreatorService
     * @param PatientAppointmentCreatorService $patientAppointmentCreatorService
     * @param PatientCreatorService $patientCreator
     * @return Response
     * @throws Exception
     */
    public function createNew(
        Request $request,
        StaffRepository $staffRepository,
        AuthUserCreatorService $authUserCreatorService,
        MedicalHistoryCreatorService $medicalHistoryCreatorService,
        PatientAppointmentCreatorService $patientAppointmentCreatorService,
        PatientCreatorService $patientCreator
    ): Response
    {
        $staff = $staffRepository->getStaff($this->getUser());
        $patientAuthUser = $authUserCreatorService->createAuthUser();
        $patient = $patientCreator->createPatient();
        if ((new AuthUserInfoService())->isDoctorHospital($this->getUser())) {
            $staff = $staffRepository->getStaff($this->getUser());
            $patient
                ->setHospital($staff->getHospital())
                ->setCity($staff->getHospital()->getCity());
            $isDoctorHospital = true;
        }

        $clinicalDiagnosis = new ClinicalDiagnosis();
        $medicalHistory = $medicalHistoryCreatorService->createMedicalHistory()->setClinicalDiagnosis($clinicalDiagnosis);
        $firstPatientAppointment = $patientAppointmentCreatorService->createPatientAppointment($medicalHistory);
        $clinicalDiagnosis->setEnabled(true);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData(AuthUserRequiredType::class, $patientAuthUser),
                new FormData(PatientRequiredType::class, $patient, [self::IS_DOCTOR_HOSPITAL => $isDoctorHospital ?? null]),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(AppointmentTypeType::class, $firstPatientAppointment),

            ],
            function (EntityActions $actions)
            use (
                $patientAuthUser,
                $patient,
                $medicalHistory,
                $firstPatientAppointment,
                $staff,
                $patientCreator,
                $authUserCreatorService,
                $clinicalDiagnosis
            ) {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $authUserCreatorService->persistNewPatientAuthUser($patientAuthUser);
                    $em->flush();
                    $patientCreator
                        ->persistNewPatient($patient, $patientAuthUser, $medicalHistory, $firstPatientAppointment, $staff);
                    $em->persist($clinicalDiagnosis);
                    $em->flush();
                    $em->getConnection()->commit();
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
                $this->setRedirectMedicalHistoryRoute($patient->getId());
            }
        );
    }
}