<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\TemplateType;
use App\Entity\TextByTemplate;
use App\Form\Admin\MedicalHistory\AnamnesOfLifeType;
use App\Form\Admin\MedicalHistory\DiseaseHistoryType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\TextBySelectingTemplateType;
use App\Form\TextByTemplateType;
use App\Repository\MedicalHistoryRepository;
use App\Repository\PatientAppointmentRepository;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateRepository;
use App\Repository\TemplateTypeRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\InitialExaminationTemplate;
use App\Services\TextTemplateService\TextTemplateService;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class InitialExaminationController
 * Первичный прием
 * @Route("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory
 */
class InitialExaminationController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    //Route names
    /** @var string Route of edit anamnestic data page */
    private const EDIT_ANAMNESTIC_DATA_ROUTE = 'doctor_edit_anamnestic_data';
    /** @var string Route of edit objective data page */
    private const EDIT_OBJECTIVE_DATA_REDIRECT_ROUTE = 'edit_initial_examination_data';

    //Parameters of route
    /** @var string Id parameter of edit anamnestic data route in doctor office */
    private const EDIT_ANAMNESTIC_DATA_ROUTE_ID_PARAMETER = 'id';

    //Template names
    /** @var string Name of form template edit anamnesis of life */
    private const EDIT_ANAMNESTIC_DATA_ANAMNESIS_OF_LIFE_TEMPLATE_NAME = 'edit_initial_examination_data_anamnesis_of_life';
    /** @var string Name of form template edit objective status */
    private const EDIT_OBJECTIVE_DATA_OBJECTIVE_STATUS_TEMPLATE_NAME = 'edit_initial_examination_data_objective_status';

    /** @var string Name of form with template data */
    private const FORM_TEMPLATE_NAME = 'template';

    //Ids of template type
    /** @var int Id of Anamnesis of life template type */
    private const TEMPLATE_TYPE_ID_ANAMNESIS_LIFE = 1;
    /** @var int Id of Objective status template type */
    private const TEMPLATE_TYPE_ID_OBJECTIVE_STATUS = 3;

    /** @var string Name of form template edit OBJECTIVE data */
    private const EDIT_OBJECTIVE_DATA_TEMPLATE_NAME = 'edit_initial_examination_data';

    /**
     * InitialExaminationController constructor.
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
        $this->templateService = new InitialExaminationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Edit objective data
     * @param Request $request
     * @param Patient $patient
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws \ReflectionException
     * @throws Exception
     * @Route(
     *     "/{id}/medical_history/edit_initial_examination_data",
     *     name="edit_initial_examination_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     */
    public function editInitialExamination(
        Request $request,
        Patient $patient,
        PatientAppointmentRepository $patientAppointmentRepository,
        MedicalHistoryRepository $medicalHistoryRepository
    )
    {
        $firstAppointment = $patientAppointmentRepository->getFirstAppointment(
            $medicalHistoryRepository->getCurrentMedicalHistory($patient)
        );
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        $objectiveStatus = $firstAppointment->getObjectiveStatus();
        return $this->responseEditMultiForm(
            $request,
            $firstAppointment,
            [
                new FormData(
                    $firstAppointment,
                    PatientAppointmentType::class,
                    [PatientAppointmentType::OBJECTIVE_STATUS_TEXT_OPTION_NAME => $objectiveStatus]
                ),
                new FormData(
                    $firstAppointment->getMedicalHistory(),
                    AnamnesOfLifeType::class,
                    [
                        'anamnesOfLifeText' =>
                            $firstAppointment->getMedicalHistory()->getLifeHistory() ?
                                $firstAppointment->getMedicalHistory()->getLifeHistory()->getText()
                                : ''
                    ]
                ),
                new FormData($firstAppointment->getMedicalHistory(), DiseaseHistoryType::class),
            ],
            null,
            self::EDIT_OBJECTIVE_DATA_TEMPLATE_NAME
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @Route("/{id}/medical_history/edit_initial_examination_data/objective_status_using_constructor",
     *     name="doctor_edit_initial_examination_data_objective_status_using_constructor",
     *     methods={"GET","POST"})
     */
    public function editObjectiveDataObjectiveStatusUsingConstructor(
        Request $request,
        Patient $patient,
        PatientAppointmentRepository $patientAppointmentRepository,
        MedicalHistoryRepository $medicalHistoryRepository,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService
    )
    {
        $firstAppointment = $patientAppointmentRepository->getFirstAppointment(
            $medicalHistoryRepository->getCurrentMedicalHistory($patient)
        );
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
     * @param Patient $patient
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TemplateRepository $templateRepository
     * @param TextTemplateService $textTemplateService
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws Exception
     * @Route(
     *     "/{id}/medical_history/edit_initial_examination_data/objective_status_by_template",
     *     name="doctor_edit_initial_examination_data_objective_status_by_template",
     *     methods={"GET","POST"}
     *     )
     */
    public function editObjectiveDataObjectiveStatusByTemplate(
        Request $request,
        Patient $patient,
        TemplateTypeRepository $templateTypeRepository,
        TemplateRepository $templateRepository,
        TextTemplateService $textTemplateService,
        PatientAppointmentRepository $patientAppointmentRepository,
        MedicalHistoryRepository $medicalHistoryRepository
    )
    {
        $firstAppointment = $patientAppointmentRepository->getFirstAppointment(
            $medicalHistoryRepository->getCurrentMedicalHistory($patient)
        );
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
     * @param Request $request
     * @param Patient $patient
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TextTemplateService $textTemplateService
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @Route(
     *     "/{id}/medical_history/edit_initial_examination_data/anamnesis_of_life_using_constructor",
     *     name="doctor_edit_initial_examination_data_anamnesis_of_life_using_constructor",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     */
    public function editInitialExaminationDataAnamnesisOfLifeUsingConstructor(
        Request $request,
        Patient $patient,
        TemplateTypeRepository $templateTypeRepository,
        TemplateParameterRepository $templateParameterRepository,
        TextTemplateService $textTemplateService,
        MedicalHistoryRepository $medicalHistoryRepository,
        PatientAppointmentRepository $patientAppointmentRepository
    )
    {
        $this->templateService->setRedirectRoute(
            'edit_initial_examination_data',
            [
                'id' => $patientAppointmentRepository->getFirstAppointment(
                    $medicalHistoryRepository->getCurrentMedicalHistory($patient)
                )->getId()
            ]
        );
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
     *     "/{id}/medical_history/edit_initial_examination_data/anamnesis_of_life_by_template",
     *     name="doctor_edit_initial_examination_data_anamnesis_of_life_by_template",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     */
    public function editInitialExaminationDataAnamnesisOfLifeByTemplate(
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