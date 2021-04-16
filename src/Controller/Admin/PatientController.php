<?php

namespace App\Controller\Admin;

use App\Entity\ClinicalDiagnosis;
use App\Form\AuthUser\AuthUserEmailType;
use App\Form\AuthUser\AuthUserEnabledType;
use App\Form\AuthUser\AuthUserPasswordType;
use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\Admin\Patient\PatientClinicalDiagnosisTextType;
use App\Form\Admin\Patient\PatientMKBCodeType;
use App\Form\Admin\Patient\PatientOptionalType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Repository\MedicalHistoryRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use App\Services\EntityActions\Creator\MedicalHistoryCreatorService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PatientCreatorService;
use App\Services\DataTable\Admin\PatientDataTableService;
use App\Entity\Patient;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Управление страницами пациентов
 * @Route("/admin/patient")
 * @IsGranted("ROLE_ADMIN")
 */
class PatientController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/patient/';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * PatientController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new PatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список пациентов
     * @Route("/", name="patient_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, PatientDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новый пациент
     * @Route("/new", name="patient_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @param AuthUserCreatorService $authUserCreatorService
     * @param MedicalHistoryCreatorService $medicalHistoryCreatorService
     * @param PatientAppointmentCreatorService $patientAppointmentCreatorService
     * @param PatientCreatorService $patientCreator
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        AuthUserCreatorService $authUserCreatorService,
        MedicalHistoryCreatorService $medicalHistoryCreatorService,
        PatientAppointmentCreatorService $patientAppointmentCreatorService,
        PatientCreatorService $patientCreator
    ): Response
    {
        $clinicalDiagnosis = new ClinicalDiagnosis();
        $authUser = $authUserCreatorService->createAuthUser();
        $patient = $patientCreator->createPatient();
        $medicalHistory = $medicalHistoryCreatorService->createMedicalHistory()->setClinicalDiagnosis($clinicalDiagnosis);
        $patientAppointment = $patientAppointmentCreatorService->createPatientAppointment($medicalHistory);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(
                    AuthUserPasswordType::class,
                    $authUser,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                ),
                new FormData(PatientRequiredType::class, $patient),
                new FormData(PatientOptionalType::class, $patient),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(StaffType::class, $patientAppointment),
                new FormData(AppointmentTypeType::class, $patientAppointment),
            ],
            function (EntityActions $actions)
            use (
                $authUser,
                $patient,
                $medicalHistory,
                $patientAppointment,
                $patientCreator,
                $authUserCreatorService,
                $clinicalDiagnosis
            ) {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $authUserCreatorService->persistNewPatientAuthUser($authUser);
                    $em->flush();
                    $patientCreator
                        ->persistNewPatient(
                            $patient, $authUser, $medicalHistory, $patientAppointment, $patientAppointment->getStaff()
                        );
                    $clinicalDiagnosis->setEnabled(true);
                    $em->persist($clinicalDiagnosis);
                    $em->flush();
                    $em->getConnection()->commit();
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
            }
        );
    }

    /**
     * Информация о пациенте
     * @Route("/{id}", name="patient_show", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Patient $patient
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function show(Patient $patient, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'bodyMassIndex' => PatientInfoService::getBodyMassIndex($patient),
                'medicalHistoryFilterName' =>
                    $filterService->generateFilterName('medical_history_list', Patient::class),
            ]
        );
    }

    /**
     * Редактирование пациента
     * @Route("/{id}/edit", name="patient_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return Response
     * @throws ReflectionException
     */
    public function edit(
        Request $request,
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository
    ): Response
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $clinicalDiagnosis = $medicalHistory->getClinicalDiagnosis();
        $authUser = $patient->getAuthUser();
        $oldPassword = $authUser->getPassword();
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(AuthUserPasswordType::class, $authUser, ['isPasswordRequired' => false]),
                new FormData(PatientRequiredType::class, $patient),
                new FormData(PatientOptionalType::class, $patient),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
            ],
            function () use ($authUser, $oldPassword, $patient, $clinicalDiagnosis) {
                AuthUserCreatorService::updatePassword($this->passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
            }
        );
    }

    /**
     * Удаление пациента
     * @Route("/{id}", name="patient_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Patient $patient
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Patient $patient): Response
    {
        return $this->responseDelete($request, $patient);
    }
}
