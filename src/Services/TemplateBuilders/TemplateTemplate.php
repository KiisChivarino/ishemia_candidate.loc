<?php

namespace App\Services\TemplateBuilders;

use App\Controller\AppAbstractController;
use App\Entity\TemplateType;
use App\Repository\TemplateTypeRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
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
            self::COMMON_CONTENT
        );
    }

    /**
     * Builds list template
     *
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AdminTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->setPath($this->getTemplatePath());
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
//            ->setFilters(
//                $filterService,
//                [
//                    new TemplateFilter(
//                        AppAbstractController::FILTER_LABELS['TEMPLATE_TYPE'],
//                        TemplateType::class,
//                        [
//                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
//                                ->getContentValue('templateTypeFilter'),
//                            'class' => TemplateType::class,
//                            'required' => false,
//                            'choice_label' => 'name',
//                            'query_builder' => function (TemplateTypeRepository $er) {
//                                return $er->createQueryBuilder('t')
//                                    ->where('t.enabled = true');
//                            },
//                        ]
//                    ),
//                ]
//            )
        ;

        return $this;
    }

    /**
     * Builds edit template
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AdminTemplateBuilder
    {
        parent::edit();
        return $this;
    }


    /**
     *  Builds show template settings of AnalysisGroup controller
     *
     * @param object|null $templateType
     * @return $this
     */
    public function show(?object $templateType = null): AdminTemplateBuilder
    {
        parent::show($templateType);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('title', 'Тип шаблона '.$templateType->getName())
            ->setContent('h1', 'Тип шаблона '.$templateType->getName());
        return $this;
    }
}