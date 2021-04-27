<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class PlanTestingTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PlanTestingTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for plan of analyzes templates */
    protected const COMMON_CONTENT = [
        'analysisGroup' => 'Группа анализов',
        'timeRangeCount' => 'Срок выполнения',
        'timeRange' => TimeRangeTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'План обследований',
        'title' => 'План обследований',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новое запланированное обследование',
        'title' => 'Новое запланированное обследование',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр запланированного обследования',
        'title' => 'Просмотр запланированного обследования',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование запланированного обследования',
        'title' => 'Редактирование запланированного обследования',
    ];
    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        'analysisGroupPlaceholder' => 'Выберите обследование',
    ];
    /** @var string[] Common form_show content */
    protected const FORM_SHOW_CONTENT = [
        'startingPoint' => StartingPointTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Запланированное обследование',
    ];

    /**
     * PlanTestingTemplate constructor.
     *
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
     * Builds list of plan testing
     * @param FilterService|null $filterService
     * @return AppTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);

        $this->onlyAdminAccessAdded();
        $this->onlyAdminAccessEdit();

        return $this;
    }

    /**
     * Builds show of plan testing
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show();

        $this->onlyAdminAccessEdit();

        return $this;
    }
}