<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Repository\MedicalHistoryRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\Admin\MedicalRecordTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;
use App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate;

/**
 * Class PatientAppointmentTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PatientAppointmentTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    public const COMMON_CONTENT = [
        'medicalRecord' => MedicalRecordTemplate::ENTITY_CONTENT['entity'],
        'appointmentTime' => 'Дата приема',
        'inclusion_time' => 'Дата и время включения в назначение',
        'isConfirmed' => 'Подтверждено',
        'staff' => 'Врач создавший назначение',
        'appointmentType' => 'Вид приема',
        'plannedDateTime' => 'Дата приема по плану'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Приемы пациентов',
        'title' => 'Список приема пациентов',
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
        'medicalHistory' => MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
        'plannedTimeDateLabel' => 'Дата приема',
        'plannedTimeTimeLabel' => 'Время приема'
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
        'h1' => 'Просмотр приема пациета',
        'title' => 'Просмотр приема пациета',
        'isPatientConfirmed' => 'Подтверждено пациентом',
        'complaintsNotFound' => 'Жалобы отсутствуют',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование приема пациета',
        'title' => 'Редактирование приема пациета',
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

    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->getTemplateItemRoute()
            ->setRouteName('add_prescription_show')
            ->setRouteParams(
                [
                    'patient' => $entity->getPatientAppointment()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                ]
            );
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)->getTemplateItemRoute()
            ->setRouteName('edit_prescription_appointment_by_doctor')
            ->setRouteParams(
                [
                    'patient' => $entity->getPatientAppointment()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                    'prescriptionAppointment' => $entity->getId()
                ]
            );
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->getTemplateItemRoute()
            ->setRouteName('delete_prescription_appointment_by_doctor')
            ->setRouteParams(
                [
                    'patient' => $entity->getPatientAppointment()->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $entity->getPrescription()->getId(),
                    'prescriptionAppointment' => $entity->getId()
                ]
            );
        return $this;
    }
}