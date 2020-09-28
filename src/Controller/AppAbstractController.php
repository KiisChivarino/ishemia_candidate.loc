<?php

namespace App\Controller;

use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\AdminDatatableService;
use App\Services\Template\TemplateService;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class AppAbstractController
 *
 * @package App\Controller\Admin
 */
abstract class AppAbstractController extends AbstractController
{
    /** @var TemplateService $adminTemplateService */
    protected $templateService;

    /** @var string[] Labels of filters */
    public const FILTER_LABELS = [
        'ANALYSIS_GROUP' => 'analysisGroup',
        'PATIENT' => 'patient',
        'PATIENT_TESTING' => 'patientTesting',
        'HOSPITAL' => 'hospital',
        'MEDICAL_HISTORY' => 'medicalHistory',
        'STAFF' => 'staff',
        'PRESCRIPTION'=>'prescription',
    ];

    /** @var string Label of form option for adding formTemplateItem in form */
    public const FORM_TEMPLATE_ITEM_OPTION_TITLE = 'formTemplateItem';

    /**
     * Отображает действия с записью в таблице datatables
     *
     * @return Closure
     */
    protected function renderTableActions(): Closure
    {
        return function ($value) {
            return $this->render(
                $this->templateService->getCommonTemplatePath().'tableActions.html.twig', [
                    'rowId' => $value,
                    'template' => $this->templateService
                ]
            )->getContent();
        };
    }

    /**
     * Sets variable template for templates
     *
     * @param Environment $twig
     * @param string $varName
     */
    protected function setTemplateTwigGlobal(Environment $twig, string $varName = 'template'): void
    {
        $twig->addGlobal($varName, $this->templateService);
    }

    /**
     * Отображает шаблон вывода списка элементов с использованием datatable
     *
     * @param Request $request
     * @param AdminDatatableService $dataTableService
     * @param FilterLabels|null $filterLabels
     *
     * @return Response
     */
    public function responseList(
        Request $request,
        AdminDatatableService $dataTableService,
        ?FilterLabels $filterLabels = null
    ): Response {
        $template = $this->templateService->list($filterLabels ? $filterLabels->getFilterService() : null);
        if ($filterLabels) {
            $filters = $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray());
        }
        $table = $dataTableService->getTable(
            $this->renderTableActions(),
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME),
            $filters ?? null
        );
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render(
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->getPath().'list.html.twig', [
                'datatable' => $table,
                'filters' => $template->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getFiltersViews(),
            ]
        );
    }

    /**
     * Возвращает массив фильтров по меткам
     *
     * @param $template
     * @param $filterLabels
     *
     * @return array
     */
    protected function getFiltersByFilterLabels($template, $filterLabels): array
    {
        $filters = [];
        foreach ($filterLabels as $filterLabel) {
            $filterEntity = $template
                ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                ->getFilterDataByName($filterLabel)
                ->getEntity();
            $filters[$filterLabel] = $filterEntity ? $filterEntity : '';
        }
        return $filters;
    }

    /**
     * Show entity info
     *
     * @param string $templatePath
     * @param object $entity
     * @param array $parameters
     *
     * @return Response
     */
    public function responseShow(string $templatePath, object $entity, array $parameters = []): Response
    {
        $this->templateService->show($entity);
        $parameters['entity'] = $entity;
        return $this->render($templatePath.'show.html.twig', $parameters);
    }
}