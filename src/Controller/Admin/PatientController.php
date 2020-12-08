<?php

namespace App\Controller\Admin;

use App\Form\Admin\AuthUser\AuthUserPasswordType;
use App\Form\Admin\AuthUser\AuthUserRequiredType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientOptionalType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\Creator\MedicalHistoryCreatorService;
use App\Services\Creator\PatientAppointmentCreatorService;
use App\Services\Creator\PatientCreatorService;
use App\Services\DataTable\Admin\PatientDataTableService;
use App\Entity\Patient;
use App\Form\Admin\AuthUser\AuthUserOptionalType;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\LoggerService\LogService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @var Logger
     */
    private $logger;

    /**
     * PatientController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LogService $logger
     */
    public function __construct(Environment $twig, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, LogService $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new PatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->logger = $logger;
    }

    /**
     * Список пациентов
     * @Route("/", name="patient_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientDataTableService $dataTableService
     *
     * @return Response
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
     * @throws ReflectionException
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
        $authUser = $authUserCreatorService->createAuthUser();
        $patient = $patientCreator->createPatient();
        $medicalHistory = $medicalHistoryCreatorService->createMedicalHistory();
        $patientAppointment = $patientAppointmentCreatorService->createPatientAppointment($medicalHistory);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser, AuthUserRequiredType::class),
                new FormData($authUser, AuthUserOptionalType::class),
                new FormData(
                    $authUser,
                    AuthUserPasswordType::class,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                ),
                new FormData($patient, PatientRequiredType::class),
                new FormData($patient, PatientOptionalType::class),
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions)
            use ($authUser, $patient, $medicalHistory, $patientAppointment, $patientCreator, $authUserCreatorService) {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $authUserCreatorService->persistAuthUser($authUser);
                    $em->flush();
                    $patientCreator
                        ->persistPatient(
                            $patient, $authUser, $medicalHistory, $patientAppointment, $patientAppointment->getStaff()
                        );
                    $em->flush();
                    $logger = $this->logger
                        ->setUser($this->getUser())
                        ->setDescription(
                            'Patient with id: ' . $patient->getId() .
                            ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                            ' successfully created!')
                        ->logCreateEvent();
                    if (!$logger) {
                        $this->logger->getError();
                        // TODO:  when creating log fails
                    }
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
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        Patient $patient
    ): Response
    {
        $authUser = $patient->getAuthUser();
        $oldPassword = $authUser->getPassword();
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser, AuthUserRequiredType::class),
                new FormData($authUser, AuthUserOptionalType::class),
                new FormData($authUser, AuthUserPasswordType::class, ['isPasswordRequired' => false]),
                new FormData($patient, PatientRequiredType::class),
                new FormData($patient, PatientOptionalType::class),
            ],
            function () use ($authUser, $oldPassword, $patient) {
                $this->editPassword($this->passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
                $logger = $this->logger
                    ->setUser($this->getUser())
                    ->setDescription(
                        'Patient with id: ' . $patient->getId() .
                        ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                        ' successfully updated!')
                    ->logUpdateEvent();
                if (!$logger) {
                    $this->logger->getError();
                    // TODO:  when creating log fails
                }
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
     */
    public function delete(Request $request, Patient $patient): Response
    {
        $logger = $this->logger
            ->setUser($this->getUser())
            ->setDescription(
                'Patient with id: ' . $patient->getId() .
                ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                ' successfully updated!')
            ->logDeleteEvent();
        if (!$logger) {
            $this->logger->getError();
            // TODO:  when creating log fails
        }
        return $this->responseDelete($request, $patient);
    }
}
