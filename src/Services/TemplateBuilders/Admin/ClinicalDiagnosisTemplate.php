<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class ClinicalDiagnosisTemplate
 * @package App\Services\TemplateBuilders\Admin
 */
class ClinicalDiagnosisTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Клинические диагнозы',
        'title' => 'Список клинических диагнозов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новый клинический диагноз',
        'title' => 'Новый клинический диагноз',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр клинического диагноза',
        'title' => 'Просмотр клинического диагноза',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование клинического диагноза',
        'title' => 'Редактирование клинического диагноза',
    ];

    /** @var string[] Common COMMON_CONTENT */
    public const COMMON_CONTENT = [
        'text' => 'Текст клинического диагноза',
        'MKBCode' => 'Код клинического диагноза',
    ];

    /** @var string[] Common FORM_CONTENT */
    public const FORM_CONTENT = [
        'MKBCodePlaceholder' => 'Выберите код МКБ',
        'mainDiseasePlaceholder' => 'Выберите заболевание',
        'backgroundDiseasesPlaceholder' => 'Выберите фоновые заболевания',
        'complicationsPlaceholder' => 'Выберите осложнения',
        'concomitantDiseasesPlaceholder' => 'Выберите сопутствующие заболевания',
    ];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'mainDisease' => 'Основное заболевание',
        'backgroundDiseases' => 'Фоновые заболевания',
        'complications' => 'Осложнения основного заболевания',
        'concomitantDiseases' => 'Сопутствующие заболевания',
    ];

    /**
     * ClinicalDiagnosisTemplate constructor.
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