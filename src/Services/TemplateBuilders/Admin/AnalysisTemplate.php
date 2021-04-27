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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AnalysisTemplate
 * builds template settings of Analysis controller
 *
 * @package App\Services\TemplateBuilders
 */
class AnalysisTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'analysisGroup' => 'Группа анализов'
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Анализы',
        'title' => 'Список анализов',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый анализ',
        'title' => 'Новый анализ',
    ];

    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'h1' => 'Анализ',
        'title' => 'Просмотр анализа',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование анализа',
        'title' => 'Редактирование анализа',
    ];

    /** @var string[] Common filter content for staff templates */
    protected const FILTER_CONTENT = [
        'analysisGroupFilter' => 'Фильтр группы анализов',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Анализ',
    ];

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($routeCollection, $className, $authorizationChecker);
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
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
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
                                return $er->createQueryBuilder('d')
                                    ->where('d.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}