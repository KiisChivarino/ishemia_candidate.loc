<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RoleTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class RoleTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'tech_name' => 'Техническое название'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Роли',
        'title' => 'Роли',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр роли',
        'title' => 'Просмотр роли',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование роли',
        'title' => 'Редактирование роли',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Роль',
    ];

    /**
     * RoleTemplate constructor.
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
     * Show role template
     *
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * List of roles template
     *
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * Edit role template
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}