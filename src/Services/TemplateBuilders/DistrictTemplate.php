<?php

namespace App\Services\TemplateBuilders;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class DistrictTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class DistrictTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for District templates */
    protected const COMMON_CONTENT = [
        'region' => 'Регион'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Районы',
        'title' => 'Список районов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление района',
        'title' => 'Добавление района',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр района',
        'title' => 'Просмотр района',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование района',
        'title' => 'Редактирование района',
    ];

    /**
     * DistrictTemplate constructor.
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
}