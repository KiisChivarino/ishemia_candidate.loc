<?php

namespace App\Services\TemplateBuilders;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class BlogItemTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class BlogItemTemplate extends AdminTemplateBuilder
{
    //** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'itemTitle' => 'Заголовок',
        'duration' => 'Длительность (мин).',
        'completed' => 'Выполнено',
        'blogRecord' => 'Запись в блог',
        'project' => 'Раздел',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Изменения',
        'title' => 'Список изменений',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление изменения',
        'title' => 'Добавление изменения',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр изменения',
        'h1' => 'Просмотр изменения',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактрование изменения',
        'title' => 'Редактрование изменения',
    ];

    /**
     * BlogItemTemplate constructor.
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