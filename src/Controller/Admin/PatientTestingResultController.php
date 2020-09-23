<?php

namespace App\Controller\Admin;

use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Form\Admin\PatientTestingResultType;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientTestingResultDataTableService;
use App\Services\ControllerGetters\EntityActions;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\TemplateBuilders\PatientTestingResultTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PatientTestingResultController
 * Контроллеры результатов анализа
 * @Route("/admin/patient_testing_result")
 *
 * @package App\Controller\Admin
 */
class PatientTestingResultController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_testing_result/';

    /** @var string testingDate constant */
    private const TESTING_DATE_FORM_NAME = 'testingDate';

    /**
     * PatientTestingResultController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientTestingResultTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Вывод списка результатов анализа
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param PatientTestingResultDataTableService $dataTableService
     *
     * @return Response
     * @Route("/", name="patient_testing_result_list", methods={"GET","POST"})
     */
    public function list(
        Request $request,
        FilterService $filterService,
        PatientTestingResultDataTableService $dataTableService
    ): Response {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['PATIENT'],
                    self::FILTER_LABELS['PATIENT_TESTING'],
                ]
            )
        );
    }

    /**
     * Внесение результата анализа
     *
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @param Request $request
     *
     * @return Response
     * @Route("/new", name="patient_testing_result_new", methods={"GET","POST"})
     */
    public function new(PatientTestingResultRepository $patientTestingResultRepository, Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        /** @var PatientTesting $patientTesting */
        $patientTesting = $entityManager->getRepository(PatientTesting::class)->find((int)$request->query->get('id'));
        if (is_null($patientTesting)) {
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        $analyzes = $patientTestingResultRepository->getAnalyzes($patientTesting);
        $formBuilder = $this->createFormBuilder();
        $formTemplateItem = $this->templateService->new()->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME);
        $formBuilder->add(
            self::TESTING_DATE_FORM_NAME,
            DateType::class,
            [
                'widget' => 'single_text',
                'label' => $formTemplateItem->getContentValue('testingDate'),
                'data' => $patientTesting->getAnalysisDate(),
                'required' => true
            ]
        );
        if ($patientTesting->getPatientTestingResults()->count() > 0) {
            foreach ($patientTesting->getPatientTestingResults() as $testingResult) {
                $formBuilder->add(
                    'testing'.$testingResult->getAnalysis()->getId(),
                    PatientTestingResultType::class,
                    [
                        'label' => $testingResult->getAnalysis()->getName(),
                        'patientTesting' => $patientTesting,
                        'analysis' => $testingResult->getAnalysis(),
                        'data' => $testingResult,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $formTemplateItem,
                    ]
                );
            }
        } else {
            foreach ($analyzes as $analysis) {
                $formBuilder->add(
                    'testing'.$analysis->getId(),
                    PatientTestingResultType::class,
                    [
                        'label' => $analysis->getName(),
                        'patientTesting' => $patientTesting,
                        'analysis' => $analysis,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $formTemplateItem,
                    ]
                );
            }
        }
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            /**
             * @var string $formName
             * @var PatientTestingResult $data
             */
            foreach ($formData as $formName => $data) {
                if ($formName == self::TESTING_DATE_FORM_NAME) {
                    $patientTesting->setAnalysisDate($data);
                    $entityManager->persist($patientTesting);
                } else {
                    $data->setPatientTesting($patientTesting);
                    if ($data->getResult()) {
                        $data->setEnabled(true);
                        $entityManager->persist($data);
                    }
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'new.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Вывод результата анализа
     *
     * @param PatientTestingResult $patientTestingResult
     * @Route("/{id}", name="patient_testing_result_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @return Response
     */
    public function show(PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patientTestingResult,
            [
                'patientTestingInfo' => (new PatientTestingInfoService())->getPatientTestingInfoString($patientTestingResult->getPatientTesting()),
                'analysisRateInfo' => (new AnalysisRateInfoService())->getAnalysisRateInfoString($patientTestingResult->getAnalysisRate()),
            ]
        );
    }

    /**
     * Редактирование результатов анализа
     * @Route("/{id}/edit", name="patient_testing_result_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTestingResult $patientTestingResult
     *
     * @return Response
     */
    public function edit(Request $request, PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseEdit(
            $request,
            $patientTestingResult,
            PatientTestingResultType::class,
            [
                'patientTesting' => $patientTestingResult->getPatientTesting(),
                'analysis' => $patientTestingResult->getAnalysis()
            ],
            function (EntityActions $actions) {
                if (!is_null($actions->getEntity()->getResult())) {
                    $actions->getEntity()->setEnabled(true);
                }
            }
        );
    }

    /**
     * Удаление результата анализа
     * @Route("/{id}", name="patient_testing_result_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientTestingResult $patientTestingResult
     *
     * @return Response
     */
    public function delete(Request $request, PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseDelete($request, $patientTestingResult);
    }
}
