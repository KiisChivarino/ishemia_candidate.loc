<?php

namespace App\Controller\Admin;

use App\Entity\Measure;
use App\Form\Admin\MeasureType;
use App\Services\DataTable\Admin\MeasureDataTableService;
use App\Services\TemplateBuilders\MeasureTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class MeasureController
 * Контроллеры сущности "единица измерения"
 * @Route("/admin/measure")
 *
 * @package App\Controller\Admin
 */
class MeasureController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/measure/';

    /**
     * MeasureController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new MeasureTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список единиц измерения
     * @Route("/", name="measure_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param MeasureDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, MeasureDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новая единица измерения
     * @Route("/new", name="measure_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request,
            (new Measure()),
            MeasureType::class,
            null,
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Просмотр единицы измерения
     * @Route("/{id}", name="measure_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Measure $measure
     *
     * @return Response
     */
    public function show(Measure $measure): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $measure);
    }

    /**
     * Редактрование единицы измерения
     * @Route("/{id}/edit", name="measure_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Measure $measure
     *
     * @return Response
     */
    public function edit(Request $request, Measure $measure): Response
    {
        return $this->responseEdit($request, $measure, MeasureType::class);
    }

    /**
     * Удаление единицы измерения
     * @Route("/{id}", name="measure_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Measure $measure
     *
     * @return Response
     */
    public function delete(Request $request, Measure $measure): Response
    {
        return $this->responseDelete($request, $measure);
    }
}
