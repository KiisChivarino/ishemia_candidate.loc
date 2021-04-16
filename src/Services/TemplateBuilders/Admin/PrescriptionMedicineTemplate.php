<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PrescriptionMedicineTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PrescriptionMedicineTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'prescription' => 'Назначение',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'startingMedicationDate' => 'Дата начала приема лекарства',
        'endMedicationDate' => 'Дата окончания приема лекарства',
        'patientMedicine' => PatientMedicineTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Назначенные лекарства',
        'title' => 'Список назначенных лекарств',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление назначения лекарства',
        'title' => 'Добавление назначения лекарства',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр назначения лекарства',
        'h1' => 'Просмотр назначения лекарства',
        'inclusionTime' => 'Дата и время включения лекарства в назначение',
        'notificationConfirmId' => 'Id уведомления о подтверждении'
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование назначения лекарства',
        'title' => 'Редактирование назначения лекарства',
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        'medicinePlaceholder' => 'Введите название препарата',
    ];

    /** @var string[] Common filter content */
    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Назначение лекарства',
    ];

    /** @var array Common FORM_SHOW content */
    public const FORM_SHOW_CONTENT = [
        'instruction' => 'Инструкция по применению',
    ];

    /**
     * PrescriptionMedicineTemplate constructor.
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
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['PRESCRIPTION'],
                        Prescription::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('prescriptionFilter'),
                            'class' => Prescription::class,
                            'required' => false,
                            'choice_label' => function (Prescription $value) {
                                return (new PrescriptionInfoService())->getPrescriptionTitle($value);
                            },
                            'query_builder' => function (PrescriptionRepository $er) {
                                return $er->createQueryBuilder('p')
                                    ->where('p.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}