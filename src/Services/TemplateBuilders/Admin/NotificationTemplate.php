<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Repository\PatientRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\TableActionsTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class NotificationTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class NotificationTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'notificationType' => 'Тип уведомления',
        'staff' => 'Отправивший врач',
        'notificationTime' => 'Дата и время отправки',
        'text' => 'Текст',
        'patient' => 'Пациент',
        'from' => 'Отправитель',
        'smsNotification' => 'SMS уведомление'
    ];
    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'text' => 'Текст уведомления',
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Уведомления',
        'title' => 'Список уведомлений',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление уведомления',
        'title' => 'Добавление уведомления',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр уведомления',
        'title' => 'Просмотр уведомления',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование уведомления',
        'title' => 'Редактирование уведомления',
    ];

    protected const FILTER_CONTENT = [
        'patientFilter' => 'Пациент'
    ];

    /**
     * NotificationTemplate constructor.
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
        $this->getItem(TableActionsTemplateItem::TEMPLATE_ITEM_SHOW_ACTIONS_NAME)
            ->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['PATIENT'],
                        Patient::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('patientFilter'),
                            'class' => Patient::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return $value->getAuthUser()->getLastName() . ' ' . $value->getAuthUser()->getFirstName();
                            },
                            'query_builder' => function (PatientRepository $er) {
                                return $er->createQueryBuilder('p')
                                    ->leftJoin('p.AuthUser', 'au')
                                    ->where('au.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
    /**
     * Builds edit template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     * Builds show template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}