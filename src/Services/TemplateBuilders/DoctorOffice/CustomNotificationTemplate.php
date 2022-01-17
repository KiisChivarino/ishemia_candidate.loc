<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CustomNotificationTemplate
 *
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class CustomNotificationTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common FORM_CONTENT */
    protected const FORM_CONTENT = [
        "text" => "Текст сообщения",
        'formButtonLabel' => 'Отправить',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Новое сообщение',
        'title' => 'Новое сообщение',
    ];

    /**
     * CustomNotificationTemplate constructor.
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
            self::FILTER_CONTENT,
            self::ENTITY_CONTENT
        );
    }

    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit($entity);
        return $this;
    }

}