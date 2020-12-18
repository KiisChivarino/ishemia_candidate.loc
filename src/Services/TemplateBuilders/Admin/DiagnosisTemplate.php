<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class DiagnosisTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class DiagnosisTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for Diagnosis templates */
    protected const COMMON_CONTENT = [
        'code' => 'Код диагноза',
        'parentCode' => 'Код верхнего уровня (категория)'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Диагнозы',
        'title' => 'Список диагнозов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новый диагноз',
        'title' => 'Новый диагноз',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр диагноза',
        'title' => 'Просмотр диагноза',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование диагноза',
        'title' => 'Редактирование диагноза',
    ];

    protected const ENTITY_CONTENT = [
        'entity' => 'Диагноз'
    ];

    /**
     * DiagnosisTemplate constructor.
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