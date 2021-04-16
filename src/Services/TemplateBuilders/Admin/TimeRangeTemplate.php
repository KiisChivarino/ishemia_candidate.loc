<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class TimeRangeTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class TimeRangeTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'rangeTitle' => 'Заголовок',
        'rangeTitleHelp' => 'Название временного диапазона',
        'dateInterval' => DateIntervalTemplateBuilder::ENTITY_CONTENT['entity'],
        'dateIntervalHelp' => 'Величина для множителя',
        'multiplier' => 'Множитель',
        'multiplierHelp' => 'Какое количество интервалов содержит в себе временной диапазон',
        'isRegular' => 'Регулярно',
        'isRegularHelp' => 'Флаг указывает на регулярность временного диапазона (каждая неделя, каждый месяц, каждый день)',
        'help_message' => 'Временные диапазоны используется для того, чтобы задавать время выполнения однократных (раз в месяц, раз в 3 месяца) или регулярных (каждые пол года, каждую неделю) событий (например: обследование по плану, приём по плану)'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Временные диапазоны',
        'title' => 'Список временных диапазонов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление временного диапазона',
        'title' => 'Добавление временного диапазона',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр временного диапазона',
        'title' => 'Просмотр временного диапазона',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование временного диапазона',
        'title' => 'Редакитрование временного диапазона',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Временной диапазон',
    ];


    /**
     * TimeRangeTemplate constructor.
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
}