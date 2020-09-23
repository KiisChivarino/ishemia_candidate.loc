<?php

namespace App\Services\TemplateBuilders;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Repository\MedicalHistoryRepository;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateItems\FilterTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalRecordTemplate
 * builds template settings for MedicalRecord controller
 *
 * @package App\Services\TemplateBuilders
 */
class MedicalRecordTemplate extends AdminTemplateBuilder
{

    /** @var string[] list content */
    protected const LIST_CONTENT = [
        'title' => 'Записи в историю болезни',
        'h1' => 'Список записей в историю болезни',
    ];

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'recordDate' => 'Дата записи',
        'comment' => 'Комментарий',
        'medicalHistory' => 'История болезни',
    ];

    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [];

    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'prescription' => 'Назначения',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление записи в историю болезни',
        'title' => 'Добавление записи в историю болезни',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр записи в историю болезни',
        'title' => 'Просмотр записи в историю болезни',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактрование записи в историю болезни',
        'title' => 'Редактрование записи в историю болезни',
    ];

    /** @var string[] Common FILTER_CONTENT */
    protected const FILTER_CONTENT = [
        'medicalHistoryFilter' => 'Фильтр по истории болезни',
    ];

    /**
     * MedicalRecordTemplate constructor.
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
     * Builds list template settings of MedicalRecord controller
     *
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
                        AppAbstractController::FILTER_LABELS['MEDICAL_HISTORY'],
                        MedicalHistory::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('medicalHistoryFilter'),
                            'class' => MedicalHistory::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return (new AuthUserInfoService())->getFIO($value->getPatient()->getAuthUser()).': '.
                                    $value->getDateBegin()->format('d.m.Y');
                            },
                            'query_builder' => function (MedicalHistoryRepository $er) {
                                return $er->createQueryBuilder('mh')
                                    ->leftJoin('mh.patient', 'p')
                                    ->leftJoin('p.AuthUser', 'au')
                                    ->where('au.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
}