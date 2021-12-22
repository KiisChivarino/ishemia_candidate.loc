<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Repository\AnalysisGroupRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisRateTemplate
 * Build template data for analysis rate list
 *
 * @package App\Services\TemplateBuilders
 */
class AnalysisRateTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis rate templates */
    protected const COMMON_CONTENT = [
        'analysis' => 'Анализ',
        'measure' => 'Единица измерения',
        'analysisGroup' => 'Группа анализов',
        'rateMin' => 'Минимальное значение',
        'rateMax' => 'Максимальное значение',
        'gender' => 'Пол',
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common form and show content for staff templates */
    protected const FORM_SHOW_CONTENT = [
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Референтные значения',
        'title' => 'Список референтных значений',
        'rateMin' => 'Мин.',
        'rateMax' => 'Макс.',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новое референтное значение',
        'title' => 'Новое референтное значение',
    ];

    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'h1' => 'Референтное значение',
        'title' => 'Просмотр референтных значений',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование референтных значений',
        'title' => 'Редактирование референтных значений',
    ];

    /** @var string[] Common filter content for staff templates */
    protected const FILTER_CONTENT = [
        'analysisGroupFilter' => 'Фильтр группы анализов',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Референтное значение',
    ];

    /**
     * AnalysisRateTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            self::LIST_CONTENT,
            self::NEW_CONTENT,
            self::SHOW_CONTENT,
            self::EDIT_CONTENT,
            self::FORM_CONTENT,
            self::FORM_SHOW_CONTENT,
            self::COMMON_CONTENT,
            self::FILTER_CONTENT,
            self::ENTITY_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     * @return $this|AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP'],
                        AnalysisGroup::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('analysisGroupFilter'),
                            'class' => AnalysisGroup::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'query_builder' => function (AnalysisGroupRepository $er) {
                                return $er->createQueryBuilder('ag')
                                    ->where('ag.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }

    /**
     * @param FilterService|null $filterService
     * @return $this|AppTemplateBuilder
     * @throws Exception
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['ANALYSIS_GROUP'],
                        AnalysisGroup::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('analysisGroupFilter'),
                            'class' => AnalysisGroup::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'query_builder' => function (AnalysisGroupRepository $er) {
                                return $er->createQueryBuilder('ag')
                                    ->where('ag.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}