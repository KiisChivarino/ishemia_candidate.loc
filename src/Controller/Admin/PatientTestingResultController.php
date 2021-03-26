<?php

namespace App\Controller\Admin;

use App\Entity\PatientTestingResult;
use App\Form\Admin\PatientTestingResult\EnabledType;
use App\Form\Admin\PatientTestingResultType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientTestingResultDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientTestingResultTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientTestingResultController
 * Контроллеры результатов анализа
 * @Route("/admin/patient_testing_result")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PatientTestingResultController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_testing_result/';

    /**
     * PatientTestingResultController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
     */
    public function list(
        Request $request,
        FilterService $filterService,
        PatientTestingResultDataTableService $dataTableService
    ): Response
    {
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
     * Вывод результата анализа
     *
     * @param PatientTestingResult $patientTestingResult
     * @Route("/{id}", name="patient_testing_result_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @return Response
     * @throws Exception
     */
    public function show(PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patientTestingResult,
            [
                'patientTestingInfo' =>
                    PatientTestingInfoService::getPatientTestingInfoString($patientTestingResult->getPatientTesting()),
                'analysisRateInfo' =>
                    AnalysisRateInfoService::getAnalysisRateInfoString($patientTestingResult->getAnalysisRate()),
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
     * @throws Exception
     */
    public function edit(Request $request, PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $patientTestingResult,
            [
                new FormData($patientTestingResult, PatientTestingResultType::class, [
                    'analysis' => $patientTestingResult->getAnalysis()
                ]),
                new FormData($patientTestingResult, EnabledType::class)
            ]
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
     * @throws Exception
     */
    public function delete(Request $request, PatientTestingResult $patientTestingResult): Response
    {
        return $this->responseDelete($request, $patientTestingResult);
    }
}
