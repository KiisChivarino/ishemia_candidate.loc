<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PatientTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for patient templates */
    public const COMMON_CONTENT = [
        'insuranceNumber' => 'Номер страховки',
        'dateBirth' => 'Дата рождения',
        'phone' => 'Телефон',
        'staff' => StaffTemplate::ENTITY_CONTENT['staff'],
        'appointmentType' => AppointmentTypeTemplate::ENTITY_CONTENT['appointmentType'],
    ];

    /** @var string[] Common content for form, show templates */
    public const FORM_SHOW_CONTENT = [
        'snils' => 'СНИЛС',
        'address' => 'Адрес',
        'smsInforming' => 'Информировать по смс',
        'emailInforming' => 'Информировать по email',
        'passport' => 'Паспорт',
        'weight' => 'Вес',
        'height' => 'Рост',
        'city' => 'Город',
        'district' => 'Район',
        'hospital' => 'Больница',
        'heartAttackDate' => 'Дата возникновения инфаркта',
        'passportIssuingAuthority' => 'Орган, выдавший паспорт',
        'passportIssuingAuthorityCode' => 'Код органа, выдавшего паспорт',
        'passportIssueDate' => 'Дата выдачи паспорта'
    ];

    /** @var string[] Common content for list, edit template */
    public const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
        'diagnosisPlaceholder' => 'Выберите диагноз',
        'cityPlaceholder' => 'Выберите город',
        'staffFio' => StaffTemplate::ENTITY_CONTENT['staffFio'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Пациенты',
        'title' => 'Список пациентов',
        'fio' => 'ФИО',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление пациента',
        'title' => 'Добавление пациента',
    ];

    /** @var string[] Common SHOW_CONTENT */
    public const SHOW_CONTENT = [
        'title' => 'Пациент',
        'bodyMassIndex' => 'Индекс массы тела',
//        'addMedicalHistory' => 'Добавить историю болезни',
        'medicalHistories' => 'Истории болезни',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование пациента',
        'title' => 'Редактирование пациента',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const ENTITY_CONTENT  = [
        'entity' => 'Пациент',
    ];

    /**
     * PatientTemplate constructor.
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
     * Builds new template settings of Patient controller
     *
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                    MedicalHistoryTemplate::COMMON_CONTENT
                )
            );
        return $this;
    }

    /**
     * Builds show template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show();
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT
                )
            )
            ->setContent('h1', (new AuthUserInfoService())->getFIO($entity->getAuthUser(), true));
        return $this;
    }

    /**
     * Builds edit template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT
                )
            );
        return $this;
    }
}