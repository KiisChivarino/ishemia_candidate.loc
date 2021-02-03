<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Form\Admin\PatientTesting\PatientTestingNotRequiredType;
use App\Form\Admin\PatientTestingResultType;
use App\Form\PatientTestingFileType;
use App\Repository\PatientTestingRepository;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientTestingsListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingsListHistoryDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingsListNoProcessedDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingsListOverdueDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingsListPlannedDataTableService;
use App\Services\FileService\FileService;
use App\Services\FilterService\FilterService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingsListTemplate;
use App\Services\TemplateItems\ListTemplateItem;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientsListController
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class PatientTestingsListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patient_testings_list/';

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientTestingsListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient testings
     * @Route("/patient/{id}/patient_testings", name="patient_testings_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingsListDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        Patient $patient,
        Request $request,
        PatientTestingsListDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()]
        );
    }

    /**
     * List of patient testings history
     * @Route("/patient/{id}/patient_testings_history", name="patient_testings_history_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingsListHistoryDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsHistoryList(
        Patient $patient,
        Request $request,
        PatientTestingsListHistoryDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'История списка обследований');
            }
        );
    }

    /**
     * List of not processed patient testings
     * @Route("/patient/{id}/patient_testings_not_processed", name="patient_testings_not_processed_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingsListNoProcessedDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsNotProcessedList(
        Patient $patient,
        Request $request,
        PatientTestingsListNoProcessedDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список необработанных обследований');
            }
        );
    }

    /**
     * List of overdue patient testings
     * @Route("/patient/{id}/patient_testings_overdue", name="patient_testings_overdue_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingsListOverdueDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsOverdueList(
        Patient $patient,
        Request $request,
        PatientTestingsListOverdueDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список просроченных обследований');
            }
        );
    }

    /**
     * List of planned patient testings
     * @Route("/patient/{id}/patient_testings_planned", name="patient_testings_planned_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingsListPlannedDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsPlannedList(
        Patient $patient,
        Request $request,
        PatientTestingsListPlannedDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список запланированных обследований');
            }
        );
    }

    /**
     * Редактирование анализа пациента
     * @Route("patientTesting/{id}/edit", name="patient_testing_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTestingRepository $patientTestingRepository
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return Response
     * @throws ReflectionException
     */
    public function edit(
        Request $request,
        PatientTestingRepository $patientTestingRepository,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    ): Response
    {
        $patientTesting = $patientTestingRepository->find($request->query->get('patientTestingId'));
        $this->setRedirectMedicalHistoryRoute($patientTesting->getMedicalHistory()->getPatient()->getId());
        $this->templateService->setCommonTemplatePath(self::TEMPLATE_PATH);
        $enabledTestingResults = $patientTestingResultRepository->getEnabledTestingResults($patientTesting);
        $patientTestingResultsFormData = [];
        foreach ($enabledTestingResults as $key => $patientTestingResult) {
            $patientTestingResultsFormData[] = new FormData(
                $patientTestingResult,
                PatientTestingResultType::class,
                [
                    'analysis' => $patientTestingResult->getAnalysis()
                ],
                true,
                $key
            );
        }
        return $this->responseEditMultiForm(
            $request,
            $patientTesting,
            array_merge(
                [
                    new FormData($patientTesting, PatientTestingNotRequiredType::class),
                ],
                $patientTestingResultsFormData
            ),
            function (EntityActions $actions) use ($fileService, $patientTesting) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(PatientTestingNotRequiredType::class))
                        ->get(MultiFormService::getFormName(PatientTestingFileType::class) . 's')
                );
                $patientTesting->setEnabled(true);
                $patientTesting->setProcessed(true);
            }
        );
    }
}
