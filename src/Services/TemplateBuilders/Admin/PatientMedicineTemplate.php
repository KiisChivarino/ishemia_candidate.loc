<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientMedicineTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PatientMedicineTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'prescriptionMedicine' => PrescriptionMedicineTemplate::ENTITY_CONTENT['entity'],
        'medicine' => 'Название лекарства',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'medicineName' => 'Название лекарства',
        'startingMedicationDate' => 'Дата начала приема лекарства',
        'endMedicationDate' => 'Дата окончания приема лекарства',
        'instruction' => 'Инструкция',
        'prescription' => PrescriptionTemplate::ENTITY_CONTENT['entity'],
        'patient' => MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Лекарства пациентов',
        'title' => 'Список лекарств пациентов',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление лекарства пациента',
        'title' => 'Добавление лекарства пациента',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр лекарства пациента',
        'h1' => 'Просмотр лекарства пациента',
        'inclusionTime' => 'Дата и время включения лекарства в назначение',
        'patientMedicine' => 'Лекарства пациента'
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование лекарства пациента',
        'title' => 'Редактирование лекарства пациента',
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        'medicinePlaceholder' => 'Введите название препарата',
    ];

    /** @var string[] Common form and show content */
    protected const FORM_SHOW_CONTENT = [
        'instruction' => 'Инструкция по применению',
        'receptionMethod' => 'Способ приема',
    ];

    /** @var string[] Common filter content */
    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Прием лекарства',
    ];

    /**
     * PrescriptionMedicineTemplate constructor.
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

    /**
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        return $this;
    }
}