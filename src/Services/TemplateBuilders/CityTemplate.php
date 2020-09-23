<?php

namespace App\Services\TemplateBuilders;

use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CityTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class CityTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'region' => 'Регион',
        'district' => 'Район',

    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Города',
        'title' => 'Список городов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление города',
        'title' => 'Добавление города',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр города',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактрование города',
        'title' => 'Редактрование города',
    ];

    /**
     * CityTemplate constructor.
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
            self::FILTER_CONTENT
        );
    }

    /**
     * Builds show of city
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function show(?object $entity = null): AdminTemplateBuilder
    {
        parent::show();
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('h1', 'Просмотр города '.$entity->getName());
        return $this;
    }
}