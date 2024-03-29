<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Repository\MedicalHistoryRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientAppointmentTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PatientAppointmentTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    public const COMMON_CONTENT = [
        'medicalRecord' => 'Запись в историю',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'appointmentType' => AppointmentTypeTemplate::ENTITY_CONTENT['entity'],
        'appointmentTime' => 'Дата приема',
        'isConfirmed' => 'Подтверждено',
        'isProcessedByStaff' => 'Обработано врачом',
        'isMissed' => 'Пропущено',
        'medicalHistory' => 'История болезни',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Приемы пациентов',
        'title' => 'Список приема пациентов',
        'plannedDateTime' => 'Дата приема по плану',
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
    ];
    public const FORM_CONTENT = [
        'complaintsPlaceholder' => 'Выберите жалобы',
    ];
    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление приема пациета',
        'title' => 'Добавление приема пациета',
    ];

    /** @var string[] Common SHOW_CONTENT */
    public const SHOW_CONTENT = [
        'h1' => 'Просмотр приема пациента',
        'title' => 'Просмотр приема пациента',
        'staffFio' => StaffTemplate::ENTITY_CONTENT['staffFio'],
        'isPatientConfirmed' => 'Подтверждено пациентом',
        'complaintsNotFound' => 'Жалобы отсутствуют',
        'prescriptionAppointment' => 'Назначение на прием',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование приема пациента',
        'title' => 'Редактирование приема пациента',
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
     * PatientAppointmentTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     *
     * @throws Exception
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
     * @param array|null $itemsWithRoutes
     *
     * @return AppTemplateBuilder
     *
     * @throws Exception
     */
    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY'],
                        MedicalHistory::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('medicalHistory'),
                            'class' => MedicalHistory::class,
                            'required' => false,
                            'choice_label' => function (MedicalHistory $value) {
                                return (new AuthUserInfoService())->getFIO($value->getPatient()->getAuthUser()) . ': ' . $value->getDateBegin()->format('d.m.Y');
                            },
                            'query_builder' => function (MedicalHistoryRepository $er) {
                                return $er->createQueryBuilder('mh')
                                    ->leftJoin('mh.patient', 'p')
                                    ->leftJoin('p.AuthUser', 'a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}