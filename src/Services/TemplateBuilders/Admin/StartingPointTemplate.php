<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class StartingPointTemplate
 * @package App\Services\TemplateBuilders
 */
class StartingPointTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Точки отсчета',
        'title' => 'Список точек отсчета',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр точки отсчета',
        'title' => 'Просмотр точки отсчета',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование точки отсчета',
        'title' => 'Редактирование точки отсчета',
    ];

    /**
     * StartingPointTemplate constructor.
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
            self::EDIT_CONTENT
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
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}