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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class PrescriptionAppointmentTemplate
 * @package App\Services\TemplateBuilders\Admin
 */
class PrescriptionAppointmentTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for District templates */
    protected const COMMON_CONTENT = [
        'prescription' => 'Назначение',
        'patientAppointment' => 'Прием',
        'staff' => StaffTemplate::ENTITY_CONTENT['entity'],
        'plannedDateTime' => 'Дата и время приема по плану',
        'inclusionTime' => 'Дата и время включения назначения на прием в назначение',
        'confirmedByStaff' => 'Подтверждено врачом',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Назначения на прием',
        'title' => 'Назначения на прием',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление назначения на прием',
        'title' => 'Добавление назначения на прием',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр назначения на прием',
        'title' => 'Просмотр назначения на прием',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование назначения на прием',
        'title' => 'Редактирование назначения на прием',
    ];

    /** @var string[] Common filter content */
    protected const FILTER_CONTENT = [
        'prescriptionFilter' => 'Фильтр по назначению',
    ];

    /** @var string[] Common FORM SHOW CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'plannedTimeDateLabel' => 'Дата приема по плану',
        'plannedTimeTimeLabel' => 'Время приема по плану',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Назначение на прием',
    ];

    /**
     * PrescriptionAppointmentTemplate constructor.
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($routeCollection, $className, $authorizationChecker);
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
     * @return $this|AppTemplateBuilder
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
                PatientAppointmentTemplate::COMMON_CONTENT
            );
        return $this;
    }
}