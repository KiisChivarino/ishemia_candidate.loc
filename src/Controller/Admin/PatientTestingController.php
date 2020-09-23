<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Form\Admin\PatientTesting\PatientTestingType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientTestingDataTableService;
use App\Services\ControllerGetters\EntityActions;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\PatientTestingTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PatientTestingController
 * Контроллеры проведения анализов пациента
 * @Route("/admin/patient_testing")
 *
 * @package App\Controller\Admin
 */
class PatientTestingController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_testing/';

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
    ): Response {
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
     * @return Response
     */
    public function new(Request $request): Response
    {
        $patientTesting = new PatientTesting();
        return $this->responseNew(
            $request,
            $patientTesting,
            PatientTestingType::class,
            null,
            [],
            function (EntityActions $actions) {
                /** @var MedicalHistory $medicalHistory */
                $medicalHistory = $actions->getRequest()->query->get('medical_history_id')
                    ? $this->getDoctrine()->getManager()->getRepository(MedicalHistory::class)->find($actions->getRequest()->query->get('medical_history_id'))
                    : null;
                $actions->getEntity()->setMedicalHistory($medicalHistory);
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
                'patientTestingFilterName' => $filterService->generateFilterName('patient_testing_result_list', PatientTesting::class),
                'patientFilterName' => $filterService->generateFilterName('patient_testing_result_list', Patient::class)
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
     * @return Response
     */
    public function edit(Request $request, PatientTesting $patientTesting): Response
    {
        return $this->responseEdit($request, $patientTesting, PatientTestingType::class);
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
