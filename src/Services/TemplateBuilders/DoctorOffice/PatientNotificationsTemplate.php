<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\NotificationTemplate;
use App\Repository\NotificationTemplateRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

class PatientNotificationsTemplate extends DoctorOfficeTemplateBuilder
{
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
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        "customMessage" => "Сообщение от врача",
        "doctorAppointment" => "Прием у врача",
        "confirmMedication" => "Подтверждение приема лекарств",
        "prescriptionMedicine" => 'Назначение лекарства',
        "testingAppointment" => "Сдача анализов",
        "confirmAppointment" => "Подтверждение приема",
        "submitAnalysisResults" => "Результаты анализов",
        'h1' => 'Список уведомлений',
        'title' => 'Список уведомлений',
        'fio' => 'ФИО',
        'age' => 'Возраст',
        'Status' => 'Статус',
        'confirmed' => 'Подтверждено',
        'sent' => 'Отправлено'
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр записи',
        'title' => 'Просмотр записи',
        'authUserSender' => 'Отправитель',
        'text' => 'Текст',
        'notificationReceiverType' => 'Тип получателя',
        'channelType' => 'Канал передачи',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование записи',
        'title' => 'Редактирование записи',
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

    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath());
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
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setContent(
                AppAbstractController::FILTER_LABELS['NOTIFICATION'],
                $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('label')
            )
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['NOTIFICATION'],
                        NotificationTemplate::class,
                        [
                            'class' => NotificationTemplate::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return self::LIST_CONTENT[$value->getName()];
                            },
                            'label' => false,
                            'query_builder' => function (NotificationTemplateRepository $er) {
                                return $er->createQueryBuilder('nT')
                                    ->orderBy('nT.id', 'ASC');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}