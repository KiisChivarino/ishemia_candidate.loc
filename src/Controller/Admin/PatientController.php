<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use App\Form\Admin\AuthUser\AuthUserPasswordType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Repository\PatientRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\CreatingPatient\CreatingPatientService;
use App\Services\DataTable\Admin\PatientDataTableService;
use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Form\Admin\AuthUser\AuthUserType;
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

    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * PatientController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(Environment $twig, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
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
     * @param PatientRepository $patientRepository
     * @param CreatingPatientService $creatingPatientService
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        PatientRepository $patientRepository,
        CreatingPatientService $creatingPatientService
    ): Response
    {
        $authUser = (new AuthUser())->setEnabled(true);
        $patient = (new Patient())->setAuthUser($authUser);
        $medicalHistory = (new MedicalHistory)->setPatient($patient);
        $patientAppointment = (new PatientAppointment())->setMedicalHistory($medicalHistory);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser, AuthUserType::class),
                new FormData(
                    $authUser,
                    AuthUserPasswordType::class,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => true]
                ),
                new FormData($patient, PatientType::class),
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions)
            use ($authUser, $patient, $medicalHistory, $patientAppointment, $patientRepository, $creatingPatientService) {
                $patient->getAuthUser()->setRoles(self::PATIENT_ROLE);
                $encodedPassword = $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword());
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
                $authUser->setPassword($encodedPassword);
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $em->persist($authUser);
                    $em->flush();
                    $creatingPatientService->persistPatient($patientAppointment);
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
                new FormData($authUser, AuthUserType::class),
                new FormData($authUser, AuthUserPasswordType::class, ['isPasswordRequired' => false]),
                new FormData($patient, PatientType::class),
            ],
            function () use ($authUser, $oldPassword) {
                $this->editPassword($this->passwordEncoder, $authUser, $oldPassword);
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
     */
    public function delete(Request $request, Patient $patient): Response
    {
        return $this->responseDelete($request, $patient);
    }
}
