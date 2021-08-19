<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AnalysisGroupTemplate;
use App\Services\TemplateBuilders\Admin\StaffTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingTemplate
 * prepare template data for patient testing list
 *
 * @package App\Services\TemplateBuilders
 */
class PatientTestingTemplate extends DoctorOfficeTemplateBuilder
{

    /** @var string[] Common content for PatientTesting templates */
    public const COMMON_CONTENT = [
        'analysisGroup' => AnalysisGroupTemplate::ENTITY_CONTENT['entity'],
        'plannedDate' => 'Запланировано',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление обследования',
        'title' => 'Добавление обследования',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование обследования',
        'title' => 'Редактирование обследования',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр обследования',
        'title' => 'Просмотр обследования',
        'inclusion_time' => 'Дата и время включения в назначение',
        'staff' => 'Врач создавший назначение'
    ];

    /**
     * PatientTestingTemplate constructor.
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
     * @return $this|DoctorOfficeTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * @param object|null $entity
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->getTemplateItemRoute()
            ->setRouteName('add_prescription_show')
            ->setRouteParams(
                [
                    'patient' => $entity->getPatientTesting()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                ]
            );
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)->getTemplateItemRoute()
            ->setRouteName('edit_prescription_testing_by_doctor')
            ->setRouteParams(
                [
                    'patient' => $entity->getPrescription()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                    'prescriptionTesting' => $entity->getId()
                ]
            );
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->getTemplateItemRoute()
            ->setRouteName('delete_prescription_testing_by_doctor')
            ->setRouteParams(
                [
                    'patient' => $entity->getPatientTesting()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                    'prescriptionTesting' => $entity->getId()
                ]
            );
        return $this;
    }
}