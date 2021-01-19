<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AdminTemplateBuilder;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class HospitalTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class HospitalTemplate extends DoctorOfficeTemplateBuilder
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
        'email' => 'Email'
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
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setIsEnabled(false);

        return $this;
    }
}