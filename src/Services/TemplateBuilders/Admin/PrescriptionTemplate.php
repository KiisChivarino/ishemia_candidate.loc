<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Staff;
use App\Repository\MedicalHistoryRepository;
use App\Repository\StaffRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PrescriptionTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PrescriptionTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'medicalHistory' => 'История болезни',
        'staffFio' => StaffTemplate::ENTITY_CONTENT['staffFio'],
        'createdTime' => 'Создано',
        'isCompleted' => 'Назначено',
        'isPatientConfirmed' => 'Подтверждено пациентом',

    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'completedTime' => 'Назначено (дата и время)'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Список назначений',
        'title' => 'Назначения',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новое назначение',
        'title' => 'Новое назначение',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр назначения',
        'h1' => 'Просмотр назначения',
        'medicalRecord' => 'Запись в историю болезни',
        'addPrescriptionMedicine' => 'Добавить лекарство',
        'prescriptionMedicines' => 'Лекарства',
        'addPrescriptionTesting' => 'Добавить назначение на обследование',
        'prescriptionTestings' => 'Назначения на обследование',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Изменение назначения',
        'title' => 'Изменение назначения',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'medicalHistory' => 'Фильтр по истории болезни',
        'staff' => StaffTemplate::ENTITY_CONTENT['staff'],
    ];

    /**
     * CityTemplate constructor.
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
            self::FILTER_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
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
                                return (new AuthUserInfoService())->getFIO($value->getPatient()->getAuthUser()).': '.$value->getDateBegin()->format('d.m.Y');
                            },
                            'query_builder' => function (MedicalHistoryRepository $er) {
                                return $er->createQueryBuilder('mh')
                                    ->leftJoin('mh.patient', 'p')
                                    ->leftJoin('p.AuthUser', 'a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['STAFF'],
                        Staff::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('staff'),
                            'class' => Staff::class,
                            'required' => false,
                            'choice_label' => function (Staff $value) {
                                return (new AuthUserInfoService())->getFIO($value->getAuthUser(), true);
                            },
                            'query_builder' => function (StaffRepository $er) {
                                return $er->createQueryBuilder('s')
                                    ->leftJoin('s.AuthUser', 'a')
                                    ->where('a.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}