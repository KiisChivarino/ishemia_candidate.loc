<?php

namespace App\Controller\DoctorOffice;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientDischargeEpicrisis;
use App\Entity\TemplateType;
use App\Entity\TextByTemplate;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\Admin\Patient\PatientOptionalType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\DischargeEpicrisisFileType;
use App\Form\DischargeEpicrisisType;
use App\Form\Doctor\AuthUserPersonalDataType;
use App\Form\TextBySelectingTemplateType;
use App\Form\TextByTemplateType;
use App\Repository\MedicalHistoryRepository;
use App\Repository\PatientAppointmentRepository;
use App\Repository\PatientTestingRepository;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateRepository;
use App\Repository\TemplateTypeRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\FileService\FileService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\MedicalHistoryTemplate;
use App\Services\TextTemplateService\TextTemplateService;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use ReflectionException;
use Symfony\Component\Form\FormInterface;
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

    //Route names
    /** @var string Route of edit anamnestic data page */
    private const EDIT_ANAMNESTIC_DATA_ROUTE = 'doctor_edit_anamnestic_data';
    /** @var string Route of edit objective data page */
    private const EDIT_OBJECTIVE_DATA_REDIRECT_ROUTE = 'doctor_edit_objective_data';

    //Parameters of route
    /** @var string Id parameter of edit anamnestic data route in doctor office */
    private const EDIT_ANAMNESTIC_DATA_ROUTE_ID_PARAMETER = 'id';
    /** @var string Id parameter of edit objective data route in doctor office */
    private const EDIT_OBJECTIVE_DATA_ROUTE_ID_PARAMETER = 'id';

    //Template names
    /** @var string Name of form template edit anamnesis of life */
    private const EDIT_ANAMNESTIC_DATA_ANAMNESIS_OF_LIFE_TEMPLATE_NAME = 'edit_anamnestic_data_anamnesis_of_life';
    /** @var string Name of form template edit objective status */
    private const EDIT_OBJECTIVE_DATA_OBJECTIVE_STATUS_TEMPLATE_NAME = 'edit_objective_data_objective_status';
    /** @var string Name of form template edit personal data */
    private const EDIT_PERSONAL_DATA_TEMPLATE_NAME = 'edit_personal_data';
    /** @var string Name of form template edit ANAMNESTIC data */
    private const EDIT_ANAMNESTIC_DATA_TEMPLATE_NAME = 'edit_anamnestic_data';
    /** @var string Name of form template edit OBJECTIVE data */
    private const EDIT_OBJECTIVE_DATA_TEMPLATE_NAME = 'edit_objective_data';
    /** @var string Name of form template edit DISCHARGE_EPICRISIS data */
    private const EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME = 'edit_discharge_epicrisis';
    /** @var string Name of form template: new discharge epicrisis */
    private const NEW_DISCHARGE_EPICRISIS_TEMPLATE_NAME = 'new_discharge_epicrisis';

    //form data names
    /** @var string Name of form with objective status data */
    private const FORM_OBJECTIVE_STATUS_NAME = 'objectiveStatus';
    /** @var string Name of form with template data */
    private const FORM_TEMPLATE_NAME = 'template';

    //Ids of template type
    /** @var int Id of Anamnesis of life template type */
    private const TEMPLATE_TYPE_ID_ANAMNESIS_LIFE = 1;
    /** @var int Id of Objective status template type */
    private const TEMPLATE_TYPE_ID_OBJECTIVE_STATUS = 3;

    /**
     * MedicalHistoryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router
    )
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * @Route("/{id}/medical_history", name="doctor_medical_history", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     *
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @param PatientTestingRepository $patientTestingRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function main(
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository,
        PatientAppointmentRepository $patientAppointmentRepository,
        PatientTestingRepository $patientTestingRepository
    ): Response
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $firstAppointment = null;
        $firstTestings = [];
        $dischargeEpicrisis = null;
        if ($medicalHistory) {
            $firstAppointment = $patientAppointmentRepository->getFirstAppointment($medicalHistory);
            $firstTestings = $patientTestingRepository->getFirstTestings($medicalHistory);
            $dischargeEpicrisis = $medicalHistory->getPatientDischargeEpicrisis();
        }
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'age' => PatientInfoService::getAge($patient),
                'medicalHistory' => $medicalHistory,
                'firstAppointment' => $firstAppointment,
                'firstTestings' => $firstTestings,
                'patientDischargeEpicrisis' => $dischargeEpicrisis,
            ]
        );
    }

    /**
     * Edit personal data of patient medical history
     * @Route(
     *     "/{id}/edit_personal_data",
     *     name="doctor_edit_personal_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param Patient $patient
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function editPersonalData(
        Request $request,
        Patient $patient,
        UserPasswordEncoderInterface $passwordEncoder
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
                new FormData($patient, PatientRequiredType::class),
                new FormData($patient, PatientOptionalType::class),
            ],
            function () use ($authUser, $oldPassword, $passwordEncoder) {
                AuthUserCreatorService::updatePassword($passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
            },
            self::EDIT_PERSONAL_DATA_TEMPLATE_NAME
        );
    }

    /**
     * Edit anamnestic data
     * @param Request $request
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     * @Route(
     *     "/{id}/edit_anamnestic_data",
     *     name="doctor_edit_anamnestic_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     */
    public function editAnamnesticData(
        Request $request,
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository
    )
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        /** @var TextByTemplate $lifeHistory */
        $lifeHistory = $medicalHistory->getLifeHistory();
        $lifeAnamnesisText = $lifeHistory ? $lifeHistory->getText() : null;
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData($medicalHistory, MainDiseaseType::class),
                new FormData(
                    $medicalHistory,
                    MedicalHistoryType::class,
                    [
                        MedicalHistoryType::ANAMNES_OF_LIFE_TEXT_OPTION_KEY => $lifeAnamnesisText,
                    ]
                ),
            ],
            function (EntityActions $actions) use ($lifeHistory) {
                $lifeHistoryText = $actions->getForm()
                    ->get(MultiFormService::getFormName(MedicalHistoryType::class))
                    ->get(MedicalHistoryType::FORM_LIFE_HISTORY_NAME)
                    ->getData();
                $lifeHistory->setText($lifeHistoryText);
            },
            self::EDIT_ANAMNESTIC_DATA_TEMPLATE_NAME
        );
    }

    /**
     * Edit objective data
     * @param Request $request
     * @param PatientAppointment $firstAppointment
     * @return RedirectResponse|Response
     * @Route(
     *     "/edit_objective_data/{id}/",
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
        $objectiveStatus = $firstAppointment->getObjectiveStatus();
        return $this->responseEdit(
            $request,
            $firstAppointment,
            PatientAppointmentType::class,
            [
                PatientAppointmentType::OBJECTIVE_STATUS_TEXT_OPTION_NAME => $objectiveStatus
            ],
            function (EntityActions $actions) use ($firstAppointment) {
                $objectiveStatusText = $actions->getForm()->has(self::FORM_OBJECTIVE_STATUS_NAME) ?
                    $actions->getForm()->get(self::FORM_OBJECTIVE_STATUS_NAME)->getData() : null;
                $firstAppointment->getObjectiveStatus()->setText($objectiveStatusText);
            },
            self::EDIT_OBJECTIVE_DATA_TEMPLATE_NAME
        );
    }

    /**
     * @Route(
     *     "/{id}/new_discharge_epicrisis",
     *     name="doctor_new_discharge_epicrisis",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     * )
     * @param Request $request
     * @param MedicalHistory $medicalHistory
     * @param FileService $fileService
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newDischargeEpicrisis(
        Request $request,
        MedicalHistory $medicalHistory,
        FileService $fileService
    )
    {
        $this->setRedirectMedicalHistoryRoute($medicalHistory->getPatient()->getId());
        return $this->responseNew(
            $request,
            (new PatientDischargeEpicrisis())->setMedicalHistory($medicalHistory),
            DischargeEpicrisisType::class,
            null,
            [],
            function (EntityActions $actions) use ($fileService) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisFileType::class) . 's')
                );
            },
            self::NEW_DISCHARGE_EPICRISIS_TEMPLATE_NAME
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
     * @param FileService $fileService
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editDischargeEpicrisis(
        Request $request,
        PatientDischargeEpicrisis $dischargeEpicrisis,
        FileService $fileService
    )
    {
        $this->setRedirectMedicalHistoryRoute($dischargeEpicrisis->getMedicalHistory()->getPatient()->getId());
        return $this->responseEdit(
            $request,
            $dischargeEpicrisis,
            DischargeEpicrisisType::class,
            [],
            function (EntityActions $actions) use ($fileService) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisFileType::class) . 's')
                );
            },
            self::EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TextTemplateService $textTemplateService
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route(
     *     "/{id}/edit_anamnestic_data_anamnesis_of_life_using_constructor",
     *     name="doctor_edit_anamnestic_data_anamnesis_of_life_using_constructor",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     */
    public function editAnamnesticDataAnamnesisOfLifeUsingConstructor(
        Request $request,
        Patient $patient,
        TemplateTypeRepository $templateTypeRepository,
        TemplateParameterRepository $templateParameterRepository,
        TextTemplateService $textTemplateService,
        MedicalHistoryRepository $medicalHistoryRepository
    )
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $templateType = $templateTypeRepository->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_ANAMNESIS_LIFE
            ]
        );
        $parameters = $templateParameterRepository->findBy(
            [
                'templateType' => $templateType
            ]
        );
        if ($medicalHistory->getLifeHistory()) {
            $textByTemplate = $medicalHistory->getLifeHistory();
        } else {
            $textByTemplate = new TextByTemplate();
            $textByTemplate->setTemplateType($templateType);
            $medicalHistory->setLifeHistory($textByTemplate);
        }
        $this->setRedirectAnamnesticDataRoute($patient->getId());
        return $this->responseEdit(
            $request,
            $patient,
            TextByTemplateType::class,
            [
                'parameters' => $parameters
            ],
            function (EntityActions $actions) use ($textByTemplate, $textTemplateService) {
                $this->persistTextByTemplateFromForm($actions->getForm(), $textByTemplate, $textTemplateService);
            },
            self::EDIT_ANAMNESTIC_DATA_ANAMNESIS_OF_LIFE_TEMPLATE_NAME,
            $textByTemplate
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @param TemplateTypeRepository $templateTypeRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param TemplateRepository $templateRepository
     * @param TextTemplateService $textTemplateService
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route(
     *     "/{id}/edit_anamnestic_data_anamnesis_of_life_by_template",
     *     name="doctor_edit_anamnestic_data_anamnesis_of_life_by_template",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     */
    public function editAnamnesticDataAnamnesisOfLifeByTemplate(
        Request $request,
        Patient $patient,
        TemplateTypeRepository $templateTypeRepository,
        MedicalHistoryRepository $medicalHistoryRepository,
        TemplateRepository $templateRepository,
        TextTemplateService $textTemplateService
    )
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $templateType = $templateTypeRepository->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_ANAMNESIS_LIFE
            ]
        );
        if ($medicalHistory->getLifeHistory()) {
            $textByTemplate = $medicalHistory->getLifeHistory();
        } else {
            $textByTemplate = new TextByTemplate();
            $textByTemplate->setTemplateType($templateType);
            $medicalHistory->setLifeHistory($textByTemplate);
        }
        $this->setRedirectAnamnesticDataRoute($patient->getId());
        return $this->responseEdit(
            $request,
            $patient,
            TextBySelectingTemplateType::class,
            [
                TextBySelectingTemplateType::TYPE_OPTION_NAME => $templateType->getId()
            ],
            function (EntityActions $actions)
            use ($templateRepository, $textByTemplate, $textTemplateService) {
                $this->persistTextByTemplateFromTemplate($actions->getForm(), $textByTemplate, $textTemplateService);
            },
            self::EDIT_ANAMNESTIC_DATA_ANAMNESIS_OF_LIFE_TEMPLATE_NAME,
            $textByTemplate
        );
    }

    /**
     * @param Request $request
     * @param PatientAppointment $firstAppointment
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/edit_objective_data/{id}/objective_status_using_constructor",
     *     name="doctor_edit_objective_data_objective_status_using_constructor",
     *     methods={"GET","POST"})
     */
    public function editObjectiveDataObjectiveStatusUsingConstructor(
        Request $request,
        PatientAppointment $firstAppointment,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService
    )
    {
        $templateType = $templateTypeRepository->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_OBJECTIVE_STATUS
            ]
        );
        $parameters = $templateParameterRepository->findBy(
            [
                'templateType' => $templateType
            ]
        );
        if ($firstAppointment->getObjectiveStatus()) {
            $textByTemplate = $firstAppointment->getObjectiveStatus();
        } else {
            $textByTemplate = new TextByTemplate();
            $textByTemplate->setTemplateType($templateType);
            $firstAppointment->setObjectiveStatus($textByTemplate);
        }
        $this->templateService->setRedirectRoute(
            self::EDIT_OBJECTIVE_DATA_REDIRECT_ROUTE,
            [
                'id' => $firstAppointment->getId()
            ]
        );
        return $this->responseEdit(
            $request,
            $firstAppointment->getMedicalHistory()->getPatient(),
            TextByTemplateType::class,
            [
                'parameters' => $parameters
            ],
            function (EntityActions $actions) use ($textByTemplate, $textTemplateService) {
                $this->persistTextByTemplateFromForm($actions->getForm(), $textByTemplate, $textTemplateService);
            },
            self::EDIT_OBJECTIVE_DATA_OBJECTIVE_STATUS_TEMPLATE_NAME,
            $textByTemplate
        );
    }

    /**
     * @param Request $request
     * @param PatientAppointment $firstAppointment
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TemplateRepository $templateRepository
     * @param TextTemplateService $textTemplateService
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route(
     *     "/edit_objective_data/{id}/objective_status_by_template",
     *     name="doctor_edit_objective_data_objective_status_by_template",
     *     methods={"GET","POST"}
     *     )
     */
    public function editObjectiveDataObjectiveStatusByTemplate(
        Request $request,
        PatientAppointment $firstAppointment,
        TemplateTypeRepository $templateTypeRepository,
        TemplateRepository $templateRepository,
        TextTemplateService $textTemplateService
    )
    {
        /** @var TemplateType $templateType */
        $templateType = $templateTypeRepository->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_OBJECTIVE_STATUS
            ]
        );
        if ($firstAppointment->getObjectiveStatus()) {
            $textByTemplate = $firstAppointment->getObjectiveStatus();
        } else {
            $textByTemplate = new TextByTemplate();
            $textByTemplate->setTemplateType($templateType);
            $firstAppointment->setObjectiveStatus($textByTemplate);
        }
        $this->templateService->setRedirectRoute(
            self::EDIT_OBJECTIVE_DATA_REDIRECT_ROUTE,
            [
                'id' => $firstAppointment->getId()
            ]
        );
        return $this->responseEdit(
            $request,
            $firstAppointment->getMedicalHistory()->getPatient(),
            TextBySelectingTemplateType::class,
            [
                'type' => $templateType->getId()
            ],
            function (EntityActions $entityActions)
            use ($templateRepository, $textByTemplate, $textTemplateService) {
                $this->persistTextByTemplateFromTemplate(
                    $entityActions->getForm(),
                    $textByTemplate,
                    $textTemplateService
                );
            },
            self::EDIT_OBJECTIVE_DATA_OBJECTIVE_STATUS_TEMPLATE_NAME,
            $textByTemplate
        );
    }


    /**
     * Set redirect to "doctor_medical_history" when form controller successfully finishes work
     * @param int $entityId
     */
    protected function setRedirectMedicalHistoryRoute(int $entityId): void
    {
        $this->templateService->setRedirectRoute(
            self::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [
                self::DOCTOR_MEDICAL_HISTORY_ROUTE_ID_PARAMETER => $entityId
            ]
        );
    }

    /**
     * Set redirect to "doctor_edit_anamnestic_data" when form controller successfully finishes work
     * @param int $entityId
     */
    protected function setRedirectAnamnesticDataRoute(int $entityId): void
    {
        $this->templateService->setRedirectRoute(
            self::EDIT_ANAMNESTIC_DATA_ROUTE,
            [
                self::EDIT_ANAMNESTIC_DATA_ROUTE_ID_PARAMETER => $entityId
            ]
        );
    }

    /**
     * Set redirect to "doctor_edit_objective_data" when form controller successfully finishes work
     * @param int $entityId
     */
    protected function setRedirectObjectiveDataRoute(int $entityId): void
    {
        $this->templateService->setRedirectRoute(
            self::EDIT_OBJECTIVE_DATA_REDIRECT_ROUTE,
            [
                self::EDIT_OBJECTIVE_DATA_ROUTE_ID_PARAMETER => $entityId
            ]
        );
    }

    /**
     * Save text by template from form
     * @param FormInterface $form
     * @param TextByTemplate $textByTemplate
     * @param TextTemplateService $textTemplateService
     */
    protected function persistTextByTemplateFromForm(
        FormInterface $form,
        TextByTemplate $textByTemplate,
        TextTemplateService $textTemplateService
    ): void
    {
        $parameterTextArray = $textTemplateService->getParameterTextArrayFromForm($form);
        $textByParameterTextArray = $textTemplateService->getTextByParameterTextArray($parameterTextArray);
        $this->persistTextByTemplate($textByParameterTextArray, $textByTemplate);
    }

    /**
     * Saves text by template from template
     * @param FormInterface $form
     * @param TextByTemplate $textByTemplate
     * @param TextTemplateService $textTemplateService
     */
    protected function persistTextByTemplateFromTemplate(
        FormInterface $form,
        TextByTemplate $textByTemplate,
        TextTemplateService $textTemplateService
    ): void
    {
        $templateEntity = $form->get(self::FORM_TEMPLATE_NAME)->getData();
        $textByTemplate->setTemplate($templateEntity);
        $parameterTextArray = $textTemplateService->getParameterTextArrayFromTemplate($templateEntity);
        $textByParameterTextArray = $textTemplateService->getTextByParameterTextArray($parameterTextArray);
        $this->persistTextByTemplate($textByParameterTextArray, $textByTemplate);
    }

    /**
     * Saves text into objective status field
     * @param string $textByParameterTextArray
     * @param TextByTemplate $textByTemplate
     */
    protected function persistTextByTemplate(string $textByParameterTextArray, TextByTemplate $textByTemplate)
    {
        $textByTemplate->setText($textByParameterTextArray);
        $this->getDoctrine()->getManager()->persist($textByTemplate);
    }
}