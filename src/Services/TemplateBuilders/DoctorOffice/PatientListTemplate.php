<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\Hospital;
use App\Repository\HospitalRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

class PatientListTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common content for patient templates */
    protected const COMMON_CONTENT = [
        'insuranceNumber' => 'Номер страховки',
        'dateBirth' => 'Дата рождения',
        'dateStartOfTreatment' => 'Начало гестации',
        'phone' => 'Телефон',
        'diagnoses' => 'Диагнозы',
        'unprocessedTestings' => 'Показатели',
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список записей',
        'title' => 'Список пациентов',
        'fio' => 'ФИО',
        'age' => 'Возраст',
        'hospital' => 'Больница',
        'dateOfBirth' => 'Дата рождения',
        'city' => 'Город',
        'status' => 'Статус'
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новая запись',
        'title' => 'Новая запись',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр записи',
        'title' => 'Просмотр записи',
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
        'entity' => 'Список пациентов',
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
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setContent(
                AppAbstractController::FILTER_LABELS['HOSPITAL'],
                $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('label')
            )
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['HOSPITAL'],
                        Hospital::class,
                        [
                            'class' => Hospital::class,
                            'required' => false,
                            'choice_label' => 'name',
                            'label' => false,
                            'query_builder' => function (HospitalRepository $er) {
                                return $er->createQueryBuilder('h')
                                    ->where('h.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}