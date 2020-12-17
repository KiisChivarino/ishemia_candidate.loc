<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateType;
use App\Repository\TemplateTypeRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class TemplateTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'fullName' => 'Полное название',
        'templateType' => 'Тип шаблона'
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список шаблонов',
        'title' => 'Шаблоны',
        'newObjectiveStatusTemplate' => 'Создать новый шаблон типа Объективный статус',
        'newTherapyTemplate' => 'Создать новый шаблон типа Терапия',
        'newLifeAnamnesisTemplate' => 'Создать новый шаблон типа Анамнез жизни',
        'createNewTemplate' => 'Создать новый шаблон',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый шаблон',
        'title' => 'Новый шаблон',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'analyzesLink' => 'Параметры',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование шаблона',
        'title' => 'Редактирование шаблона',
    ];

    /** @var string[] Common FILTER CONTENT */
    protected const FILTER_CONTENT = [
        'templateTypeFilter' => 'Тип шаблона',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Шаблон',
    ];

    /**
     * CountryTemplate constructor.
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
     * Builds list template
     *
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->setPath($this->getTemplatePath());
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['TEMPLATE_TYPE'],
                        TemplateType::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('templateTypeFilter'),
                            'class' => TemplateType::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'query_builder' => function (TemplateTypeRepository $er) {
                                return $er->createQueryBuilder('tt')
                                    ->where('tt.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }

    public function newTemplate(TemplateType $templateType)
    {
        $this->new();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setContents([
                'h1' => 'Новый шаблон типа ' . $templateType
            ]);
        return $this;
    }

    /**
     *  Builds show template settings of AnalysisGroup controller
     *
     * @param object|null $template
     * @return $this
     */
    public function show(?object $template = null): AppTemplateBuilder
    {
        parent::show($template);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent(
                'title',
                'Шаблон' .
                ($template ? ' ' . $template->getName() : '')
            )
            ->setContent(
                'h1',
                'Шаблон' .
                ($template ? ' ' . $template->getName() : '')
            );
        return $this;
    }
}