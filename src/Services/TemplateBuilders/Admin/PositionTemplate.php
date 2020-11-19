<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class PositionTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PositionTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Должности',
        'title' => 'Список должностей',
    ];
    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новая должность',
        'title' => 'Новая должность',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'h1' => 'Должность',
        'title' => 'Должность',
    ];
    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Изменить должность',
        'title' => 'Изменить должность',
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
            self::EDIT_CONTENT
        );
    }
}