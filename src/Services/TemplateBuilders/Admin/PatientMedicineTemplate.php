<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientMedicineTemplate
 * @package App\Services\TemplateBuilders\Admin
 */
class PatientMedicineTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content */
    public const COMMON_CONTENT = [
        'medicineName' => 'Название лекарства',
        'dateBegin' => 'Дата начала приема',
        'medicalHistory' => MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список лекарств для приема',
        'title' => 'Список лекарств для приема',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление лекарства для приема',
        'title' => 'Добавление лекарства для приема',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр лекарства для приема',
        'title' => 'Просмотр лекарства для приема',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование лекарства для приема',
        'title' => 'Редактирование лекарства для приема',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Лекарство для приема',
    ];

    /** @var array Common FORM_SHOW content */
    public const FORM_SHOW_CONTENT = [
        'instruction' => 'Инструкция по применению',
    ];

    /**
     * PatientMedicineTemplate constructor.
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

    /**
     * @param FilterService|null $filterService
     * @return AppTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        return $this;
    }
}