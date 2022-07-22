<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AppointmentTypeTemplate;
use App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate;
use App\Services\TemplateBuilders\Admin\StaffTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientAppointmentListTemplate
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class PatientAppointmentListTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    public const COMMON_CONTENT = [
        'medicalRecord' => 'Запись в историю',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'appointmentType' => AppointmentTypeTemplate::ENTITY_CONTENT['entity'],
        'appointmentTime' => 'Дата приема',
        'isConfirmed' => 'Подтверждено',
        'plannedDateTime' => 'Дата и время приема',
        'isProcessedByStaff' => 'Обработано врачом',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Приемы пациентов',
        'title' => 'Список приема пациентов',
        'plannedDateTime' => 'Дата приема по плану',
        'isByPlan' => 'Приём добавлен',
        'isByPlanFalse' => 'Вручную',
        'status' => 'Статус',
        'isByPlanTrue' => 'Не вручную'
    ];

    /** @var string[]  FORM_SHOW COMMON CONTENT */
    public const FORM_SHOW_CONTENT = [
        'appointmentTimeDateLabel' => 'Дата приема',
        'appointmentTimeTimeLabel' => 'Время приема',
        'recommendation' => 'Рекомендации врача',
        'complaints' => 'Жалобы',
        'complaintsComment' => 'Комментарий к жалобам',
        'objectiveStatus' => 'Объективный статус',
        'therapy' => 'Терапия',
        'medicalHistory' => 'История болезни',
    ];
    public const FORM_CONTENT = [
        'complaintsPlaceholder' => 'Выберите жалобы',
        'endAppointmentButton' => 'Завершить прием',
    ];
    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление приема пациета',
        'title' => 'Добавление приема пациета',
    ];

    /** @var string[] Common SHOW_CONTENT */
    public const SHOW_CONTENT = [
        'h1' => 'Просмотр приема пациета',
        'title' => 'Просмотр приема пациета',
        'staffFio' => StaffTemplate::ENTITY_CONTENT['staffFio'],
        'isPatientConfirmed' => 'Подтверждено пациентом',
        'complaintsNotFound' => 'Жалобы отсутствуют',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование приема пациента',
        'title' => 'Редактирование приема пациента',
        'missingButton' => 'Приём пропущен',
    ];

    protected const FILTER_CONTENT = [
        'medicalHistoryFilter' => 'Фильтр по истории болезни',
        'medicalHistory' => MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Прием пациента',
    ];

    /**
     * PatientListTemplate constructor.
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
     * @return AppTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
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
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->setPath($this->getTemplatePath());
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }

    /**
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}
