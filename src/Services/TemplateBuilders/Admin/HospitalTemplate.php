<?php

namespace App\Services\TemplateBuilders\Admin;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class HospitalTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class HospitalTemplate extends AdminTemplateBuilder
{

    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'region' => 'Регион',
        'city' => 'Город',
    ];
    /** @var string[] Common content for new,edit,show templates */
    protected const FORM_SHOW_CONTENT = [
        'address' => 'Адрес',
        'phone' => 'Телефон',
        'description' => 'Полное наименование',
        'region' => 'Регион',
        'city' => 'Город',
        'code' => 'Код',
        'email' => 'Email',
    ];

    /** @var string[] Common content for new,edit, templates */
    protected const FORM_CONTENT = [
        'phoneHelp' => 'Введите телефон 10 цифр',
    ];
    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => 'Больницы',
        'title' => 'Список больниц',
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление больницы',
        'title' => 'Добавление больницы',
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => 'Просмотр больницы',
        'title' => 'Просмотр больницы',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование больницы',
        'title' => 'Редактирование больницы',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Больница',
    ];

    /**
     * HospitalTemplate constructor.
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