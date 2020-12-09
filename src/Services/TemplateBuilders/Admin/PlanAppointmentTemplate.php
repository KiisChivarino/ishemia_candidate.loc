<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;

class PlanAppointmentTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'timeRangeCount' => 'Срок выполнения',
        'timeRange' => TimeRangeTemplate::ENTITY_CONTENT['timeRange'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Приемы по плану',
        'title' => 'Приемы по плану',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новый прием в план',
        'title' => 'Добавление приема в план',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр приема по плану',
        'h1' => 'Просмотр приема по плану'
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование приема по плану',
        'title' => 'Редактирование приема по плану',
    ];

    protected const FORM_SHOW_CONTENT = [
        'startingPoint' => StartingPointTemplate::ENTITY_CONTENT['startingPoint'],
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
}