<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class TemplateTypeTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список типов шаблонов',
        'title' => 'Типы шаблонов',
    ];

    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'templateParametersLink' => 'Параметры шаблонов',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование типа шаблона',
        'title' => 'Редактирование типа шаблона',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Тип шаблона',
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
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     * Builds edit template
     *
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     *  Builds show template settings of AnalysisGroup controller
     * @param object|null $templateType
     * @return AppTemplateBuilder
     */
    public function show(?object $templateType = null): AppTemplateBuilder
    {
        parent::show($templateType);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('title', 'Тип шаблона '.$templateType->getName())
            ->setContent('h1', 'Тип шаблона '.$templateType->getName());
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}