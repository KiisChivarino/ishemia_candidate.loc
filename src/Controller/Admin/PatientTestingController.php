<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Form\Admin\PatientTesting\PatientTestingNotRequiredType;
use App\Form\Admin\PatientTesting\PatientTestingRequiredType;
use App\Repository\MedicalHistoryRepository;
use App\Repository\PatientTestingFileRepository;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\CreatingPatient\CreatingPatientService;
use App\Services\DataTable\Admin\PatientTestingDataTableService;
use App\Services\ControllerGetters\EntityActions;
use App\Services\FilterService\FilterService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\Admin\PatientTestingTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientTestingController
 * Контроллеры проведения анализов пациента
 * @Route("/admin/patient_testing")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PatientTestingController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_testing/';

    /** @var string Name of files collection of entity method */
    protected const FILES_COLLECTION_PROPERTY_NAME = 'patientTestingFiles';

    /**
     * PatientTestingController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список анализов пациентов
     * @Route("/", name="patient_testing_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param PatientTestingDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(
        Request $request,
        FilterService $filterService,
        PatientTestingDataTableService $dataTableService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['PATIENT'],
                    self::FILTER_LABELS['MEDICAL_HISTORY'],
                ]
            )
        );
    }

    /**
     * Новый анализ пациента
     * @Route("/new", name="patient_testing_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param PatientTestingFileRepository $patientTestingFileRepository
     * @param CreatingPatientService $creatingPatientService
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        PatientTestingResultRepository $patientTestingResultRepository,
        MedicalHistoryRepository $medicalHistoryRepository,
        PatientTestingFileRepository $patientTestingFileRepository,
        CreatingPatientService $creatingPatientService
    ): Response
    {
        $patientTesting = new PatientTesting();
        return $this->responseNewMultiForm(
            $request,
            $patientTesting,
            [
                new FormData($patientTesting, PatientTestingRequiredType::class),
                new FormData($patientTesting, PatientTestingNotRequiredType::class),
            ],
            function (EntityActions $actions)
            use ($patientTestingResultRepository, $patientTesting, $medicalHistoryRepository, $patientTestingFileRepository, $creatingPatientService) {
                /** @var MedicalHistory $medicalHistory */
                $medicalHistory =
                    $actions->getRequest()->query->get(MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)
                        ? $medicalHistoryRepository
                        ->find(
                            $actions->getRequest()->query->get(
                                MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY
                            )
                        )
                        : null;
                $actions->getEntity()->setMedicalHistory($medicalHistory);
                $this->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(PatientTestingNotRequiredType::class))
                        ->get(self::FILES_COLLECTION_PROPERTY_NAME),
                    $patientTestingFileRepository
                );
                $creatingPatientService->persistTestingResultsForTesting($patientTesting);
            }
        );
    }

    /**
     * Информация по анализу пациента
     * @Route("/{id}", name="patient_testing_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PatientTesting $patientTesting
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(PatientTesting $patientTesting, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patientTesting,
            [
                'patientTestingFilterName' =>
                    $filterService->generateFilterName(
                        'patient_testing_result_list',
                        PatientTesting::class
                    ),
                'patientFilterName' =>
                    $filterService->generateFilterName(
                        'patient_testing_result_list',
                        Patient::class
                    )
            ]
        );
    }

    /**
     * Редактирование анализа пациента
     * @Route("/{id}/edit", name="patient_testing_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTesting $patientTesting
     *
     * @param PatientTestingFileRepository $patientTestingFileRepository
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(Request $request, PatientTesting $patientTesting, PatientTestingFileRepository $patientTestingFileRepository): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $patientTesting,
            [
                new FormData($patientTesting, PatientTestingRequiredType::class),
                new FormData($patientTesting, PatientTestingNotRequiredType::class),
            ],
            function (EntityActions $actions) use ($patientTesting, $patientTestingFileRepository) {
                $this->prepareFiles($actions->getForm()
                    ->get(MultiFormService::getFormName(PatientTestingNotRequiredType::class))
                    ->get(self::FILES_COLLECTION_PROPERTY_NAME), $patientTestingFileRepository);
            }
        );
    }

    /**
     * Удаление теста
     * @Route("/{id}", name="patient_testing_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTesting $patientTesting
     *
     * @return Response
     */
    public function delete(Request $request, PatientTesting $patientTesting): Response
    {
        return $this->responseDelete($request, $patientTesting);
    }
}