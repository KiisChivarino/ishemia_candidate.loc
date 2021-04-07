<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Form\PatientTesting\PatientTestingNotRequiredType;
use App\Form\Admin\PatientTestingResultType;
use App\Form\PatientTestingFileType;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientTestingListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingListHistoryDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingListNoProcessedDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingListOverdueDataTableService;
use App\Services\DataTable\DoctorOffice\PatientTestingListPlannedDataTableService;
use App\Services\FileService\FileService;
use App\Services\FilterService\FilterService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingListTemplate;
use App\Services\TemplateItems\ListTemplateItem;
use Exception;
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
    const TEMPLATE_PATH = 'doctorOffice/patient_testing_list/';

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
        $this->templateService = new PatientTestingListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient testings
     * @Route("/patient/{id}/patient_testing", name="doctor_patient_testing_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingListDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        Patient $patient,
        Request $request,
        PatientTestingListDataTableService $dataTableService,
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
                    ->setContent('title', 'Список обследований');
            }
        );
    }

    /**
     * List of patient testings history
     * @Route("/patient/{id}/patient_testing_history", name="doctor_patient_testing_history_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingListHistoryDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsHistoryList(
        Patient $patient,
        Request $request,
        PatientTestingListHistoryDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_testing_from_history'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'История списка обследований');
            }
        );
    }

    /**
     * List of not processed patient testings
     * @Route(
     *     "/patient/{id}/patient_testing_not_processed",
     *     name="doctor_patient_testing_not_processed_list",
     *     methods={"GET","POST"}
     *     )
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingListNoProcessedDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsNotProcessedList(
        Patient $patient,
        Request $request,
        PatientTestingListNoProcessedDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_testing_from_not_processed'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список необработанных обследований');
            }
        );
    }

    /**
     * List of overdue patient testings
     * @Route("/patient/{id}/patient_testing_overdue", name="doctor_patient_testing_overdue_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingListOverdueDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function patientTestingsOverdueList(
        Patient $patient,
        Request $request,
        PatientTestingListOverdueDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_testing_from_overdue'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список просроченных обследований');
            }
        );
    }

    /**
     * List of planned patient testings
     * @Route("/patient/{id}/patient_testing_planned", name="doctor_patient_testing_planned_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientTestingListPlannedDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function patientTestingsPlannedList(
        Patient $patient,
        Request $request,
        PatientTestingListPlannedDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_testing_from_planned'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список запланированных обследований');
            }
        );
    }

    /**
     * Редактирование анализа пациента
     * @Route(
     *     "/patient/{id}/patient_testing/{patientTesting}/edit",
     *     name="doctor_patient_testing_edit",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param Request $request
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @param PatientTesting $patientTesting
     * @return Response
     * @throws ReflectionException
     */
    public function edit(
        Request $request,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository,
        PatientTesting $patientTesting
    ): Response
    {
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
                $patientTesting->setIsProcessedByStaff(true);
            }
        );
    }
}
