<?php

namespace App\Controller\DoctorOffice;

use App\Entity\PatientTesting;
use App\Form\Admin\PatientTesting\PatientTestingNotRequiredType;
use App\Form\Admin\PatientTestingResultType;
use App\Form\PatientTestingFileType;
use App\Repository\PatientRepository;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientTestingsListDataTableService;
use App\Services\FileService\FileService;
use App\Services\FilterService\FilterService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\PatientTestingsListTemplate;
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
class PatientTestingsPlannedListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patient_testings_list_no_processed/';

    /** @var string Name of files collection of entity method */
    protected const FILES_COLLECTION_PROPERTY_NAME = 'patientTestingFiles';

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
     * @Route("/patient/patient_testings_no_processed", name="patient_testings_no_processed_list", methods={"GET","POST"})
     *
     * @param PatientRepository $patientRepository
     * @param Request $request
     * @param PatientTestingsListDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        PatientRepository $patientRepository,
        Request $request,
        PatientTestingsListDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        $patient = $patientRepository->findAll()[0];
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['ANALYSIS_GROUP'],]
            ),
            ['patientId' => $patient->getId()]
        );
    }

    /**
     * Редактирование обследования пациента
     * @Route("/{id}/edit", name="patient_testing_edit_no_processed", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTesting $patientTesting
     *
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return Response
     * @throws ReflectionException
     */
    public function edit(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
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
                $patientTesting->setProcessed(true);
            }
        );
    }
}
