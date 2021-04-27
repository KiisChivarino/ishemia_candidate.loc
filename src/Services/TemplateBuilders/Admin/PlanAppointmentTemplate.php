<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PlanAppointmentTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'timeRangeCount' => 'Срок выполнения',
        'timeRange' => TimeRangeTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Приемы по плану',
        'title' => 'Приемы по плану',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Новый прием в план',
        'title' => 'Добавление приема в план',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'title' => 'Просмотр приема по плану',
        'h1' => 'Просмотр приема по плану'
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование приема по плану',
        'title' => 'Редактирование приема по плану',
    ];

    /** @var string[] Common FORM SHOW CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'startingPoint' => StartingPointTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Прием по плану',
    ];

    /**
     * CityTemplate constructor.
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
     * Builds list of plan appointment
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
     * Builds show of plan appointment
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