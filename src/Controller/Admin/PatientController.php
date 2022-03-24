<?php

namespace App\Controller\Admin;

use App\Form\AuthUser\AuthUserEmailType;
use App\Form\AuthUser\AuthUserEnabledType;
use App\Form\AuthUser\AuthUserPasswordType;
use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\Patient\PatientClinicalDiagnosisTextType;
use App\Form\Patient\PatientLocationRequiredType;
use App\Form\Patient\PatientMKBCodeType;
use App\Form\Patient\PatientOptionalType;
use App\Form\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Repository\MedicalHistoryRepository;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Core\Builder\EditorEntityActionsBuilder;
use App\Services\DataTable\Admin\PatientDataTableService;
use App\Entity\Patient;
use App\Services\EntityActions\Editor\AuthUserEditorService;
use App\Services\EntityActions\Editor\PatientEditorService;
use App\Services\EntityActions\Factory\ByAdminCreatingPatientServicesFactory;
use App\Services\FilterService\FilterService;
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
     * @param Environment                  $twig
     * @param RouterInterface              $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TranslatorInterface          $translator
     *
     * @throws Exception
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    ) {
        parent::__construct($translator);
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new PatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список пациентов
     * @Route("/", name="patient_list", methods={"GET","POST"})
     *
     * @param Request                 $request
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
     * @param Request                               $request
     * @param ByAdminCreatingPatientServicesFactory $patientCreatingFactory
     *
     * @return Response
     * @throws ReflectionException
     */
    public function new(
        Request $request,
        ByAdminCreatingPatientServicesFactory $patientCreatingFactory
    ): Response {
        $authUser = $patientCreatingFactory->getAuthUser();
        $patient = $patientCreatingFactory->getPatient();
        $clinicalDiagnosis = $patientCreatingFactory->getClinicalDiagnosis();
        $patientAppointment = $patientCreatingFactory->getPatientAppointment();

        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($patientCreatingFactory->getAuthUserCreator()),
                new CreatorEntityActionsBuilder($patientCreatingFactory->getPatientCreator()),
                new CreatorEntityActionsBuilder($patientCreatingFactory->getMedicalHistoryCreator()),
                new CreatorEntityActionsBuilder($patientCreatingFactory->getPatientAppointmentCreator()),
            ],
            [
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(
                    AuthUserPasswordType::class,
                    $authUser,
                    [
                        AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false,
                    ]
                ),
                new FormData(PatientRequiredType::class, $patient),
                new FormData(PatientLocationRequiredType::class, $patient),
                new FormData(PatientOptionalType::class, $patient),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(StaffType::class, $patientAppointment),
                new FormData(AppointmentTypeType::class, $patientAppointment),
            ],
            $patient
        );
    }

    /**
     * Информация о пациенте
     * @Route("/{patient}", name="patient_show", methods={"GET","POST"}, requirements={"patient"="\d+"})
     *
     * @param Patient       $patient
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
                'bodyMassIndex'            => PatientInfoService::getBodyMassIndex($patient),
                'medicalHistoryFilterName' =>
                    $filterService->generateFilterName('medical_history_list', Patient::class),
            ]
        );
    }

    /**
     * Редактирование пациента
     * @Route("/{patient}/edit", name="patient_edit", methods={"GET","POST"}, requirements={"patient"="\d+"})
     *
     * @param Request                  $request
     * @param Patient                  $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository
    ): Response {
        if (!$medicalHistory = $this->getCurrentMedicalHistory($patient, $medicalHistoryRepository)) {
            return $this->redirectToPatient($patient);
        }
        $clinicalDiagnosis = $medicalHistory->getClinicalDiagnosis();
        $entityManager = $this->getDoctrine()->getManager();
        $authUser = $patient->getAuthUser();

        return $this->responseEditMultiFormWithActions(
            $request,
            [
                new EditorEntityActionsBuilder(new PatientEditorService($entityManager, $patient)),
                new EditorEntityActionsBuilder(
                    new AuthUserEditorService($entityManager, $authUser, $this->passwordEncoder),
                    [
                        AuthUserEditorService::OLD_PASSWORD_OPTION => $authUser->getPassword(),
                    ]
                ),
            ],
            [
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(PatientLocationRequiredType::class, $patient),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(AuthUserPasswordType::class, $authUser, ['isPasswordRequired' => false]),
                new FormData(PatientRequiredType::class, $patient),
                new FormData(PatientOptionalType::class, $patient),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
            ]
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
