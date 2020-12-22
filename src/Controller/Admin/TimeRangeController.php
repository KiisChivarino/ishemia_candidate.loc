<?php

namespace App\Controller\Admin;

use App\Entity\TimeRange;
use App\Form\Admin\TimeRangeType;
use App\Services\DataTable\Admin\TimeRangeDataTableService;
use App\Services\TemplateBuilders\Admin\TimeRangeTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class TimeRangeController
 * @Route("/admin/time_range")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class TimeRangeController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/time_range/';

    /**
     * TimeRangeController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new TimeRangeTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List time range
     * @Route("/", name="time_range_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param TimeRangeDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, TimeRangeDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New time range
     * @Route("/new", name="time_range_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new TimeRange()),
            TimeRangeType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Show time range
     * @Route("/{id}", name="time_range_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param TimeRange $timeRange
     *
     * @return Response
     * @throws Exception
     */
    public function show(TimeRange $timeRange): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $timeRange);
    }

    /**
     * Edit time range
     * @Route("/{id}/edit", name="time_range_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TimeRange $country
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, TimeRange $country): Response
    {
        return $this->responseEdit($request, $country, TimeRangeType::class);
    }

    /**
     * Delete time range
     * @Route("/{id}", name="time_range_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param TimeRange $timeRange
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, TimeRange $timeRange): Response
    {
        return $this->responseDelete($request, $timeRange);
    }
}
