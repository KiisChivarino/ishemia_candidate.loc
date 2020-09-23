<?php

namespace App\Services\TemplateBuilders;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class NotificationTypeTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class NotificationTypeTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const FORM_SHOW_CONTENT = [
        'template' => 'Шаблон',
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
        'h1' => 'Редактрование уведомления',
        'title' => 'Редактрование уведомления',
    ];

    /**
     * NotificationTypeTemplate constructor.
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
}