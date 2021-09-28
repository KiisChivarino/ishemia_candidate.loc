<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisRate;
use App\Form\Admin\AnalysisRate\AnalysisRateGenderAjaxType;
use App\Form\Admin\AnalysisRate\AnalysisRateRateEnabledAjaxType;
use App\Form\Admin\AnalysisRate\AnalysisRateRateMaxAjaxType;
use App\Form\Admin\AnalysisRate\AnalysisRateRateMinAjaxType;
use App\Form\Admin\AnalysisRate\AnalysisRateType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\AnalysisRateDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AnalysisRateTemplate;
use App\Services\TemplateItems\EditTemplateItem;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры предельных нормальных значений
 * @Route("/admin/analysis_rate")
 * @IsGranted("ROLE_ADMIN")
 */
class AnalysisRateController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/analysis_rate/';

    /**
     * AnalysisRateController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new AnalysisRateTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List reference values
     * @Route("/", name="analysis_rate_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param AnalysisRateDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        FilterService $filterService,
        AnalysisRateDataTableService $dataTableService): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray([self::FILTER_LABELS['ANALYSIS_GROUP']])
        );
    }

    /**
     * New reference values item
     * @Route("/new", name="analysis_rate_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FilterService $filterService): Response
    {
        return $this->responseNew(
            $request,
            (new AnalysisRate()),
            AnalysisRateType::class,
            (new FilterLabels($filterService))->setFilterLabelsArray([AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP']]),
            [],
            $this->setNextEntityIdFunction()
        );
    }

    /**
     * Show reference values item
     * @Route("/{id}", name="analysis_rate_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function show(AnalysisRate $analysisRate): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $analysisRate);
    }

    /**
     * Edit reference values item
     * @Route("/{id}/edit", name="analysis_rate_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, AnalysisRate $analysisRate): Response
    {
        return $this->responseEdit($request, $analysisRate, AnalysisRateType::class);
    }

    /**
     * Delete reference values item
     * @Route("/{id}", name="analysis_rate_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, AnalysisRate $analysisRate): Response
    {
        return $this->responseDelete($request, $analysisRate);
    }

    /**
     * Max analysis rate ajax edit
     * @Route(
     *     "/{id}/max_edit",
     *     name="analysis_rate_max_ajax_edit",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function maxAnalysisRateAjaxEdit(Request $request, AnalysisRate $analysisRate): Response
    {
        $this->templateService->edit();
        return $this->submitFormForAjax(
            $request,
            $analysisRate,
            AnalysisRateRateMaxAjaxType::class,
            function (FormInterface $form): string{
                return $form->getData()->getRateMax();
            }
        );
    }

    /**
     * Max analysis rate ajax edit
     * @Route(
     *     "/{id}/min_edit",
     *     name="analysis_rate_min_ajax_edit",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function minAnalysisRateAjaxEdit(Request $request, AnalysisRate $analysisRate): Response
    {
        //Инициализация темплейта
        $this->templateService->edit();
        //Рендер формы/ответа от формы
        return $this->submitFormForAjax(
            $request,
            $analysisRate,
            AnalysisRateRateMinAjaxType::class,
            function (FormInterface $form): string{
                return $form->getData()->getRateMin();
            }
        );
    }

    /**
     * Max analysis rate ajax edit
     * @Route(
     *     "/{id}/enable_edit",
     *     name="analysis_rate_enable_ajax_edit",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function enableAnalysisRateAjaxEdit(Request $request, AnalysisRate $analysisRate): Response
    {
        //Инициализация темплейта
        $this->templateService->edit();
        //Рендер формы/ответа от формы
        return $this->submitFormForAjax(
            $request,
            $analysisRate,
            AnalysisRateRateEnabledAjaxType::class,
            function (FormInterface $form): string{
                $template = $this
                    ->templateService
                    ->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME);
                return ($form->getData()->getEnabled())
                    ? $template->getContentValue('trueValue')
                    : $template->getContentValue('falseValue');
            }
        );
    }

    /**
     * Gender analysis rate ajax edit
     * @Route(
     *     "/{id}/gender_edit",
     *     name="analysis_rate_gender_ajax_edit",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param Request $request
     * @param AnalysisRate $analysisRate
     *
     * @return Response
     * @throws Exception
     */
    public function genderAnalysisRateAjaxEdit(Request $request, AnalysisRate $analysisRate): Response
    {
        //Инициализация темплейта
        $this->templateService->edit();
        //Рендер формы/ответа от формы
        return $this->submitFormForAjax(
            $request,
            $analysisRate,
            AnalysisRateGenderAjaxType::class,
            function (FormInterface $form): ?string{
                $formData = $form->getData();
                return $formData->getGender()
                    ? $formData->getGender()->getName()
                    : $this
                        ->templateService
                        ->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
                        ->getContentValue('empty');
            }
        );
    }
}