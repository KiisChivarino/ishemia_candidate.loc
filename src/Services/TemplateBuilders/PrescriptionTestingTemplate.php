<?php

namespace App\Services\TemplateBuilders;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PrescriptionTestingTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PrescriptionTestingTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for District templates */
    protected const COMMON_CONTENT = [
        'prescription' => 'Назначение',
        'patientTesting' => 'Обследование',
        'staff' => 'Врач',
        'inclusionTime' => 'Дата и время назначения'
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Назначение на обследование',
        'title' => 'Обследования',
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

    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
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
     *
     * @return $this|AdminTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AdminTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                PatientTestingTemplate::COMMON_CONTENT
            );
        return $this;
    }
}