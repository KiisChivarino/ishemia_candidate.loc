<?php


namespace App\Controller\DoctorOffice\MedicalHistory;


use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\TextByTemplate;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Repository\MedicalHistoryRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\ClinicalDiagnosisTemplate;
use ReflectionException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ClinicalDiagnosisController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    /** @var string Name of form template edit Clinical Diagnosis data */
    private const EDIT_ANAMNESTIC_DATA_TEMPLATE_NAME = 'edit_clinical_diagnosis_data';

    /**
     * MedicalHistoryController constructor.
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
        $this->templateService = new ClinicalDiagnosisTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Edit clinical diagnosis data
     * @param Request $request
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     * @Route(
     *     "/{id}/edit_clinical_diagnosis_data",
     *     name="doctor_edit_clinical_diagnosis_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     */
    public function edit(
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
}