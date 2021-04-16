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
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PrescriptionTestingTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PrescriptionTestingTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for District templates */
    public const COMMON_CONTENT = [
        'prescription' => 'Назначение',
        'patientTesting' => 'Обследование',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'plannedDate' => 'Запланировано',
        'analysisGroup' => 'Группа анализов',
        'confirmedByStaff' => 'Подтверждено врачом'
    ];
    /** @var string[] FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'inclusionTime' => 'Дата и время включения в назначение',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Назначения на обследование',
        'title' => 'Назначения на обследование',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление назначения на обследование',
        'title' => 'Добавление назначения на обследование',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр назначения на обследование',
        'title' => 'Просмотр назначения на обследование',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование назначения на обследование',
        'title' => 'Редактирование назначения на обследование',
    ];
    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Назначение на обследование',
    ];

    /**
     * PrescriptionTestingTemplate constructor.
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

    /**
     * @param FilterService|null $filterService
     * @return $this|AppTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                PatientTestingTemplate::COMMON_CONTENT
            );
        return $this;
    }
}