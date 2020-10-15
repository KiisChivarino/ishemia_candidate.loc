<?php

namespace App\Services\TemplateBuilders;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class PlanTestingTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PlanTestingTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for plan of analyzes templates */
    protected const COMMON_CONTENT = [
        'analysisGroup' => 'Группа анализов',
        'timeRangeCount' => 'Срок выполнения',
        'timeRange' => TimeRangeTemplate::DIRECTORY_CONTENT['timeRange'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'План обследований',
        'title' => 'План обследований',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новое запланированное обследование',
        'title' => 'Новое запланированное обследование',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр запланированного обследования',
        'title' => 'Просмотр запланированного обследования',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование запланированного обследования',
        'title' => 'Редактирование запланированного обследования',
    ];

    /**
     * PlanTestingTemplate constructor.
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