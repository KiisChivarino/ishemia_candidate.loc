<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RegionTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class RegionTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'country' => 'Страна',
        'region_number' => 'Код региона',
    ];
    /** @var string[] Common list content for staff templates */
    public const LIST_CONTENT = [
        'h1' => 'Регионы',
        'title' => 'Регионы',
    ];
    /** @var string[] Common new content for staff templates */
    public const NEW_CONTENT = [
        'h1' => 'Новый регион',
        'title' => 'Новый регион',
    ];
    /** @var string[] Common show content for staff templates */
    public const SHOW_CONTENT = [
        'title' => 'Просмотр региона',
        'oktmoRegionId' => 'Код ОКТМО',

    ];
    /** @var string[] Common edit content for staff templates */
    public const EDIT_CONTENT = [
        'title' => 'Редактирование региона',
        'h1' => 'Редактирование региона',
    ];

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
     * Builds show template settings of Region controller
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('h1', 'Просмотр региона ' . $entity->getName());
        return $this;
    }
}