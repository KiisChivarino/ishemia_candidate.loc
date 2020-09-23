<?php

namespace App\Services\TemplateBuilders;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateItems\FilterTemplateItem;
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
        'medicine' => 'Препарат',
        'staff' => 'Врач',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Назначенные лекарства',
        'title' => 'Список назначенных лекарст',
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
        'inclusionTime' => 'Дата и время включения лекарства в назначение'
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактрование назначения лекарства',
        'title' => 'Редактрование назначения лекарства',
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        'medicinePlaceholder' => 'Введите название препарата',
    ];

    /** @var string[] Common form and show content */
    protected const FORM_SHOW_CONTENT = [
        'instruction' => 'Инструкция по применению',
        'receptionMethod' => 'Способ приема',
    ];

    /** @var string[] Common filter content */
    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
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
            self::FILTER_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AdminTemplateBuilder
    {
        parent::list();
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['PRESCRIPTION'],
                        Prescription::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getContentValue('prescriptionFilter'),
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