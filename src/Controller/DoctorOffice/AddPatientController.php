<?php

namespace App\Controller\DoctorOffice;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Form\Admin\AuthUser\AuthUserRequiredType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Doctor\MainDiseaseInputType;
use App\Repository\StaffRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\CreatingPatient\CreatingPatientService;
use App\Services\InfoService\AuthUserInfoService;
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
    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';
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
     * @param CreatingPatientService $creatingPatientService
     * @param StaffRepository $staffRepository
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function createNew(
        Request $request,
        CreatingPatientService $creatingPatientService,
        StaffRepository $staffRepository
    ): Response
    {
        $staff = $staffRepository->getStaff($this->getUser());
        $patientAuthUser = new AuthUser();
        $patient = new Patient();
        $medicalHistory = (new MedicalHistory);
        $firstPatientAppointment = (new PatientAppointment());
        return $this->responseNewMultiForm(
            $request,
            $patient,
            [
                new FormData($patientAuthUser,AuthUserRequiredType::class),
                new FormData($patient, PatientRequiredType::class),
                new FormData($medicalHistory, MainDiseaseInputType::class),
//                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($firstPatientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions)
            use (
                $patientAuthUser,
                $patient,
                $medicalHistory,
                $firstPatientAppointment,
                $creatingPatientService,
                $staff
            )
            {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $patientAuthUser->setEnabled(true)
                        ->setPassword(AuthUserInfoService::randomPassword())
                        ->setRoles(self::PATIENT_ROLE)
                        ->setPhone(AuthUserInfoService::clearUserPhone($patientAuthUser->getPhone()));
                    $em->persist($patientAuthUser);
                    $em->flush();
                    $creatingPatientService
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