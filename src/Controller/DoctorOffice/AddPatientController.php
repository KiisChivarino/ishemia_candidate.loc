<?php

namespace App\Controller\DoctorOffice;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Form\Admin\AuthUser\AuthUserPasswordType;
use App\Form\Admin\AuthUser\AuthUserType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    const TEMPLATE_PATH = 'doctorOffice/create_patient/';

    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new CreateNewPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Add newPatient
     * @Route("/create_patient", name="create_patients", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AuthUserInfoService $authUserInfoService
     * @return Response
     * @throws Exception
     */
    public function createNew(Request $request, AuthUserInfoService $authUserInfoService): Response
    {
        $authUser = (new AuthUser())->setEnabled(true);
        $patient = (new Patient())->setAuthUser($authUser);
        $medicalHistory = (new MedicalHistory)->setPatient($patient);
        $patientAppointment = (new PatientAppointment())->setMedicalHistory($medicalHistory);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser,AuthUserType::class),
                new FormData($authUser,AuthUserPasswordType::class,
                [
                    AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => true
                ]
                ),
                new FormData($patient, PatientType::class),
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions)
            use ($authUser, $patient, $authUserInfoService, $medicalHistory, $patientAppointment)
            {
                $authUser->setRoles(self::PATIENT_ROLE);
                $encodedPassword = $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword());
                $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
                $authUser->setPassword($encodedPassword);
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $em->persist($authUser);
                    $em->flush();
                    $em->getRepository(Patient::class)->persistPatient(
                        $patient,
                        $authUser,
                        $medicalHistory,
                        $patientAppointment
                    );
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