<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AnalysisGroupTemplate;
use App\Services\TemplateBuilders\Admin\PrescriptionAppointmentTemplate;
use App\Services\TemplateBuilders\Admin\PrescriptionMedicineTemplate;
use App\Services\TemplateBuilders\Admin\PrescriptionTemplate;
use App\Services\TemplateBuilders\Admin\PrescriptionTestingTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AddPatientPrescriptionTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class AddPatientPrescriptionTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[]  */
    protected const FORM_CONTENT = [
        'medicineName' => 'Название лекарства',
        'instruction' => 'Инструкция по применению',
        'dateBegin' => 'Планируемая дата начала приема лекарства',
    ];

    /** @var array */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр назначения',
        'h2' => 'Просмотр назначения',
        'createdTime' => 'Дата и время создания назначения',
        'doctor' => 'Врач',
        'analysisGroup' => AnalysisGroupTemplate::ENTITY_CONTENT['entity'],
        'plannedDate' => PrescriptionTestingTemplate::COMMON_CONTENT['plannedDate'],
        'operations' => ListTemplateItem::DEFAULT_CONTENT['operations'],
        'loadTableData' => ListTemplateItem::DEFAULT_CONTENT['loadTableData'],
        'plannedDateTime' => PrescriptionAppointmentTemplate::COMMON_CONTENT['plannedDateTime'],
        'appointmentType' => PatientAppointmentTemplate::COMMON_CONTENT['appointmentType'],
        'startingMedicationDate' => PrescriptionMedicineTemplate::ENTITY_CONTENT['startingMedicationDate'],
        'endMedicationDate' => PrescriptionMedicineTemplate::ENTITY_CONTENT['endMedicationDate'],
        'medicine_name' => PatientMedicineTemplate::COMMON_CONTENT['medicineName'],
        'instruction' => PatientMedicineTemplate::COMMON_CONTENT['instruction'],
    ];

    /**
     * AddPatientPrescriptionTemplate constructor.
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
            PrescriptionTemplate::ENTITY_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     * @return AppTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * @param object|null $entity
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}