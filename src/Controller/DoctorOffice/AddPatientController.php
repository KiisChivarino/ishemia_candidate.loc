<?php

namespace App\Controller\DoctorOffice;

use App\Form\Admin\AuthUser\AuthUserRequiredType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Repository\StaffRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\Creator\MedicalHistoryCreatorService;
use App\Services\Creator\PatientAppointmentCreatorService;
use App\Services\Creator\PatientCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use ReflectionException;
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
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/create_patient/';

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
     * @Route("/create_patient", name="adding_patient_by_doctor", methods={"GET","POST"})
     *
     * @param Request $request
     * @param StaffRepository $staffRepository
     * @param AuthUserCreatorService $authUserCreatorService
     * @param MedicalHistoryCreatorService $medicalHistoryCreatorService
     * @param PatientAppointmentCreatorService $patientAppointmentCreatorService
     * @param PatientCreatorService $patientCreator
     * @return Response
     * @throws ReflectionException
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
        $medicalHistory = $medicalHistoryCreatorService->createMedicalHistory();
        $firstPatientAppointment = $patientAppointmentCreatorService->createPatientAppointment($medicalHistory);
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData($patientAuthUser,AuthUserRequiredType::class),
                new FormData($patient, PatientRequiredType::class),
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($firstPatientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions)
            use (
                $patientAuthUser,
                $patient,
                $medicalHistory,
                $firstPatientAppointment,
                $staff,
                $patientCreator,
                $authUserCreatorService
            )
            {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $authUserCreatorService->persistAuthUser($patientAuthUser);
                    $em->flush();
                    $patientCreator
                        ->persistPatient($patient, $patientAuthUser, $medicalHistory, $firstPatientAppointment, $staff);
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