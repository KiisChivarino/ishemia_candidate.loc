<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateType;
use App\Repository\TemplateTypeRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class TemplateParameterTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'templateType' => 'Тип шаблона'
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список параметров шаблонов',
        'title' => 'Параметры шаблонов',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый параметр шаблона',
        'title' => 'Новый параметр шаблона',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'templateParameterTextsLink' => 'Тексты параметров',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование параметра шаблона',
        'title' => 'Редактирование параметра шаблона',
    ];

    /** @var string[] Common FILTER CONTENT */
    protected const FILTER_CONTENT = [
        'templateTypeFilter' => 'Тип шаблона',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Параметр шаблона',
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

    /**
     *  Builds show template settings of AnalysisGroup controller
     * @param object|null $templateParameter
     * @return AppTemplateBuilder
     */
    public function show(?object $templateParameter = null): AppTemplateBuilder
    {
        parent::show($templateParameter);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent(
                'title',
                'Параметр шаблона' .
                ($templateParameter ? ' ' . $templateParameter->getName() : '')
            )
            ->setContent(
                'h1',
                'Параметр шаблона ' .
                ($templateParameter ? $templateParameter->getName() : '')
            );
        return $this;
    }
}