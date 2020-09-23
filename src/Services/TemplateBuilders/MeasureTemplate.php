<?php

namespace App\Services\TemplateBuilders;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class MeasureTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class MeasureTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'nameRu' => 'Русское название',
        'nameEn' => 'Междунар. название',
        'measureTitle' => 'Описание'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Единицы измерения',
        'title' => 'Список единиц измерения',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление единицы измерения',
        'title' => 'Добавление единицы измерения',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр единицы измерения',
        'title' => 'Просмотр единицы измерения',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование единицы измерения',
        'title' => 'Редактирование единицы измерения',
    ];

    /**
     * MeasureTemplate constructor.
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