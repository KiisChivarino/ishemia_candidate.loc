<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class PositionTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class PositionTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Должности',
        'title' => 'Список должностей',
    ];
    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новая должность',
        'title' => 'Новая должность',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'h1' => 'Должность',
        'title' => 'Должность',
    ];
    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Изменить должность',
        'title' => 'Изменить должность',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Должность',
    ];

    /**
     * CountryTemplate constructor.
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
}