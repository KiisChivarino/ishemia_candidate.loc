<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateParameter;
use App\Repository\TemplateParameterRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class TemplateParameterTextTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'text' => 'Текст',
        'templateParameter' => 'Параметр шаблона'
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список текстов параметров шаблонов',
        'title' => 'Тексты Параметры шаблонов',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый текст параметра шаблона',
        'title' => 'Новый текст параметра шаблона',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'analyzesLink' => 'Параметры',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование текста параметра шаблона',
        'title' => 'Редактирование текста параметра шаблона',
    ];

    /** @var string[] Common FILTER CONTENT */
    protected const FILTER_CONTENT = [
        'templateParameterFilter' => 'Фильтр по параметру шаблона',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Текст параметра шаблона',
    ];

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     *
     * @throws Exception
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
     * @param array|null $itemsWithRoutes
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['TEMPLATE_PARAMETER'],
                        TemplateParameter::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('templateParameterFilter'),
                            'class' => TemplateParameter::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'query_builder' => function (TemplateParameterRepository $er) {
                                return $er->createQueryBuilder('tp')
                                    ->where('tp.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }

    /**
     * Builds edit template
     *
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        return $this;
    }

    /**
     *  Builds show template settings of AnalysisGroup controller
     *
     * @param object|null $templateParameter
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $templateParameter = null): AppTemplateBuilder
    {
        parent::show($templateParameter);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('title', 'Параметр шаблона ' . $templateParameter->getText())
            ->setContent('h1', 'Параметр шаблона ' . $templateParameter->getText());
        return $this;
    }
}