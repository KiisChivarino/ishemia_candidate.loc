<?php

namespace App\Controller\Admin;

use App\Entity\PlanTesting;
use App\Form\Admin\PlanTesting\PlanTestingType;
use App\Services\DataTable\Admin\PlanTestingDataTableService;
use App\Services\TemplateBuilders\PlanTestingTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PlanTestingController
 * Контроллеры стандартного плана анализов для пациента
 * @Route("/admin/plan_testing")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PlanTestingController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/plan_testing/';

    /**
     * PlanTestingController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PlanTEstingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }
    /**
     * Список планируемых тестов
     * @Route("/", name="plan_testing_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PlanTestingDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, PlanTestingDataTableService $dataTableService): Response {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Добавление теста в план
     * @Route("/new", name="plan_testing_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new PlanTesting()), PlanTestingType::class);
    }

    /**
     * Просмотр планируемого теста
     * @Route("/{id}", name="plan_testing_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PlanTesting $planTesting
     *
     * @return Response
     */
    public function show(PlanTesting $planTesting): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $planTesting);
    }

    /**
     * Редактирование планируемого теста
     * @Route("/{id}/edit", name="plan_testing_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PlanTesting $planTesting
     *
     * @return Response
     */
    public function edit(Request $request, PlanTesting $planTesting): Response
    {
        return $this->responseEdit($request, $planTesting, PlanTestingType::class);
    }

    /**
     * Удаление планируемого теста
     * @Route("/{id}", name="plan_testing_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PlanTesting $planTesting
     *
     * @return Response
     */
    public function delete(Request $request, PlanTesting $planTesting): Response
    {
        return $this->responseDelete($request, $planTesting);
    }
}
