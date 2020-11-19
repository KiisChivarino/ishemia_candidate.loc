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
        'medicalRecord' => 'История болезни',
        'staff' => 'Отправивший врач',
        'notificationTime' => 'Дата и время отправки',
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
        'medicalHistory' => 'История болезни',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование уведомления',
        'title' => 'Редактирование уведомления',
    ];

    protected const FILTER_CONTENT = [
        'medicalHistory' => 'Фильтр по истории болезни',
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