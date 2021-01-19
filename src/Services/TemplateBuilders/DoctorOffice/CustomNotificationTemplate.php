<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\NotificationTemplate;
use App\Repository\NotificationTemplateRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\PatientAppointmentTemplate;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use App\Services\TemplateBuilders\Admin\PatientTestingResultTemplate;
use App\Services\TemplateBuilders\Admin\PatientTestingTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

class CustomNotificationTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Переводы названий типов уведомлений */
    const NOTIFICATION_TYPES = [
        "customMessage" => "Сообщение от врача",
        "doctorAppointment" => "Прием у врача",
        "confirmMedication" => "Подтверждение приема лекарств",
        "testingAppointment" => "Сдача анализов",
        "confirmAppointment" => "Подтверждение приема",
        "submitAnalysisResults" => "Результаты анализов",
        "text" => "Текст сообщения"
    ];

    /** @var string[] Common content for patient templates */
    protected const COMMON_CONTENT = [
        'insuranceNumber' => 'Номер страховки',
        'dateBirth' => 'Дата рождения',
        'dateStartOfTreatment' => 'Начало гестации',
        'phone' => 'Телефон',
        'diagnoses' => 'Диагнозы',
        'unprocessedTestings' => 'Показатели',
        'staff' => 'Отправитель',
        'notificationTime' => 'Дата и время отправки',
        'receiver' => 'Пациент получать',
        'channels' => 'Каналы доставки',
        'notificationType' => 'Тип уведомления'
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        "text" => "Текст сообщения"
    ];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        "text" => "Текст сообщения"
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список записей',
        'title' => 'Список записей',
        'fio' => 'ФИО',
        'age' => 'Возраст',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новая запись',
        'title' => 'Новая запись',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Новое сообщение',
        'title' => 'Новое сообщение',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Новое сообщение',
        'title' => 'Новое сообщение',
    ];
    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'label' => 'Фильтр по пациенту',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Список уведомлений',
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
    //$this->setRedirectRoute('patient_sms_list');
    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->setRedirectRoute('patients_list');
        return $this;
    }


}