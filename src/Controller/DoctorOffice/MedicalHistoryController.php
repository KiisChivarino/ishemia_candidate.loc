<?php

namespace App\Controller\DoctorOffice;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientDischargeEpicrisis;
use App\Entity\PatientTesting;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\DischargeEpicrisisFileType;
use App\Form\DischargeEpicrisisType;
use App\Form\Doctor\AuthUserPersonalDataType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\MedicalHistoryTemplate;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MedicalHistoryController
 * @Route("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class MedicalHistoryController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    /** @var string Name of form template edit personal data */
    private const EDIT_PERSONAL_DATA_TEMPLATE_NAME = 'edit_personal_data';

    /** @var string Name of form template edit ANAMNESTIC data */
    private const EDIT_ANAMNESTIC_DATA_TEMPLATE_NAME = 'edit_anamnestic_data';

    /** @var string Name of form template edit OBJECTIVE data */
    private const EDIT_OBJECTIVE_DATA_TEMPLATE_NAME = 'edit_objective_data';

    /** @var string Name of form template edit DISCHARGE_EPICRISIS data */
    private const EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME = 'edit_discharge_epicrisis';

    /** @var string Name of rout to patient medical history in doctor office */
    public const DOCTOR_MEDICAL_HISTORY_ROUTE = 'doctor_medical_history';

    /** @var AuthUserInfoService $authUserInfoService */
    private $authUserInfoService;

    /** @var PatientInfoService $patientInfoService */
    private $patientInfoService;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * MedicalHistoryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param AuthUserInfoService $authUserInfoService
     * @param PatientInfoService $patientInfoService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        AuthUserInfoService $authUserInfoService,
        PatientInfoService $patientInfoService,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->authUserInfoService = $authUserInfoService;
        $this->patientInfoService = $patientInfoService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/{id}/medical_history", name="doctor_medical_history", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     *
     * @return Response
     */
    public function main(Patient $patient): Response
    {
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)
            ->getCurrentMedicalHistory($patient);
        $firstAppointment = null;
        $firstTestings = [];
        $dischargeEpicrisis = null;
        if ($medicalHistory) {
            $firstAppointment = $this->getDoctrine()->getRepository(PatientAppointment::class)
                ->getFirstAppointment($medicalHistory);
            $firstTestings = $this->getDoctrine()->getRepository(PatientTesting::class)
                ->getFirstTestings($medicalHistory);
            $dischargeEpicrisis = $medicalHistory->getPatientDischargeEpicrisis();
        }
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'age' => $this->patientInfoService->getAge($patient),
                'medicalHistory' => $medicalHistory,
                'firstAppointment' => $firstAppointment,
                'firstTestings' => $firstTestings,
                'patientDischargeEpicrisis' => $dischargeEpicrisis,
            ]
        );
    }

    /**
     * Edit personal data of patient medical history
     * @Route("/{id}/edit_personal_data", name="doctor_edit_personal_data", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Patient $patient
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     * @throws Exception
     */
    public function editPersonalData(
        Request $request,
        Patient $patient,
        AuthUserInfoService $authUserInfoService
    ): Response
    {
        $authUser = $patient->getAuthUser();
        $oldPassword = $authUser->getPassword();
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser, AuthUserPersonalDataType::class),
                new FormData($patient, PatientType::class),
            ],
            function () use ($authUser, $oldPassword, $authUserInfoService) {
                $this->editPassword($this->passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
            },
            self::EDIT_PERSONAL_DATA_TEMPLATE_NAME
        );
    }

    /**
     * Edit anamnestic data
     * @param Request $request
     * @param Patient $patient
     * @Route("/{id}/edit_anamnestic_data", name="doctor_edit_anamnestic_data", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editAnamnesticData(Request $request, Patient $patient)
    {
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)
            ->getCurrentMedicalHistory($patient);
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData($medicalHistory, MedicalHistoryType::class),
            ],
            null,
            self::EDIT_ANAMNESTIC_DATA_TEMPLATE_NAME
        );
    }

    /**
     * Edit objective data
     * @param Request $request
     * @param PatientAppointment $firstAppointment
     * @return RedirectResponse|Response
     * @Route(
     *     "/{id}/edit_objective_data",
     *     name="doctor_edit_objective_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @throws Exception
     */
    public function editObjectiveData(Request $request, PatientAppointment $firstAppointment)
    {
        $this->setRedirectMedicalHistoryRoute($firstAppointment->getMedicalHistory()->getPatient()->getId());
        return $this->responseEdit(
            $request,
            $firstAppointment,
            PatientAppointmentType::class,
            [],
            null,
            self::EDIT_OBJECTIVE_DATA_TEMPLATE_NAME
        );
    }

    /**
     * Edit discharge epicrisis
     * @Route(
     *     "/{id}/edit_discharge_epicrisis",
     *     name="doctor_edit_discharge_epicrisis",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param PatientDischargeEpicrisis $dischargeEpicrisis
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editDischargeEpicrisis(Request $request, PatientDischargeEpicrisis $dischargeEpicrisis)
    {
        $this->setRedirectMedicalHistoryRoute($dischargeEpicrisis->getMedicalHistory()->getPatient()->getId());
        return $this->responseEdit(
            $request,
            $dischargeEpicrisis,
            DischargeEpicrisisType::class,
            [],
            function (EntityActions $actions) {
                $this->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisFileType::class) . 's')
                );
            },
            self::EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME
        );
    }
}